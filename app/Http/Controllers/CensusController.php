<?php

namespace App\Http\Controllers;

use App\Models\commune;
use App\Models\District;
use App\Models\GroupSchool;
use App\Models\Province;
use App\Models\School;
use App\Models\Teacher;
use App\Models\village;
use App\Models\Year;
use App\Support\SchoolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CensusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schoolId = SchoolAccess::schoolId();
        $censusRecords = DB::table('census')
            ->join('year', 'census.year_id', '=', 'year.id')
            ->join('schools', 'census.school_id', '=', 'schools.id')
            ->join('village', 'census.village_id', '=', 'village.id')
            ->select(
                'census.*',
                'year.year',
                'year.description as year_description',
                'schools.name as school_name',
                'village.name as village_name'
            )
            ->when($schoolId !== null, fn ($query) => $query->where('census.school_id', $schoolId))
            ->orderByDesc('census.id')
            ->get();

        $teacherNames = DB::table('census_teacher')
            ->join('teacher', 'census_teacher.teacher_id', '=', 'teacher.id')
            ->select('census_teacher.census_id', 'teacher.name')
            ->when($schoolId !== null, fn ($query) => $query->whereIn(
                'census_teacher.census_id',
                DB::table('census')->select('id')->where('school_id', $schoolId)
            ))
            ->when($schoolId !== null, fn ($query) => $query->where('teacher.school_id', $schoolId))
            ->orderBy('teacher.name')
            ->get()
            ->groupBy('census_id');

        $censusRecords->each(function ($record) use ($teacherNames) {
            $record->teacher_name = ($teacherNames->get($record->id) ?? collect())
                ->pluck('name')
                ->implode(', ');
        });

        return view('census.index', compact('censusRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schoolId = SchoolAccess::schoolId();
        $adminSchool = $schoolId === null
            ? null
            : School::with(['groupSchool', 'villages.commune.district.province'])
                ->findOrFail($schoolId);
        $defaultLocation = $adminSchool
            ? $this->getSchoolDefaultLocation($adminSchool)
            : [];
        $years = Year::where('is_active', true)->orderBy('year')->get();
        $groupSchools = GroupSchool::query()
            ->when($schoolId !== null, fn ($query) => $query->whereHas('schools', fn ($schools) => $schools->where('schools.id', $schoolId)))
            ->orderBy('name')->get();
        $schools = SchoolAccess::schoolQuery()
            ->with('villages.commune.district.province')
            ->orderBy('name')
            ->get();
        $provinces = Province::with('districts.communes.villages')->orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $communes = commune::orderBy('name')->get();
        $villages = village::orderBy('name')->get();
        $teachers = SchoolAccess::teacherQuery()->orderBy('name')->get();

        $locationTree = $provinces->map(fn ($province) => [
            'id' => $province->id,
            'name' => $province->name,
            'districts' => $province->districts->map(fn ($district) => [
                'id' => $district->id,
                'name' => $district->name,
                'communes' => $district->communes->map(fn ($commune) => [
                    'id' => $commune->id,
                    'name' => $commune->name,
                    'villages' => $commune->villages->map(fn ($village) => [
                        'id' => $village->id,
                        'name' => $village->name,
                    ])->values(),
                ])->values(),
            ])->values(),
        ])->values();

        $schoolOptions = $this->buildSchoolOptions($schools);

        $groupSchoolOptions = $groupSchools->map(fn ($groupSchool) => [
            'id' => $groupSchool->id,
            'name' => $groupSchool->name,
            'address' => $groupSchool->address ?? '',
        ])->values();

        $teacherOptions = $teachers->map(fn ($teacher) => [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'school_id' => $teacher->school_id,
        ])->values();

        return view('census.create', compact(
            'years',
            'groupSchools',
            'schools',
            'provinces',
            'districts',
            'communes',
            'villages',
            'teachers',
            'locationTree',
            'schoolOptions',
            'groupSchoolOptions',
            'teacherOptions',
            'adminSchool',
            'defaultLocation'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = SchoolAccess::schoolId();
        $adminSchool = $schoolId === null ? null : School::with('villages.commune.district.province')->findOrFail($schoolId);
        $data = $request->validate([
            'year_id' => ['required', Rule::exists('year', 'id')->where('is_active', true)],
            'group_school_id' => 'required|exists:group_schools,id',
            'school_id' => 'required|exists:schools,id',
            'province_id' => 'required|exists:province,id',
            'district_id' => 'required|exists:district,id',
            'commune_id' => 'required|exists:commune,id',
            'village_id' => [
                'required',
                $adminSchool && $adminSchool->villages->isNotEmpty()
                    ? Rule::exists('school_village', 'village_id')->where('school_id', $schoolId)
                    : Rule::exists('village', 'id'),
            ],
            'teacher_id' => 'required|array|min:1',
            'teacher_id.*' => 'required|exists:teacher,id',
            'note' => 'nullable|string|max:255',
        ]);

        if ($adminSchool) {
            $data['school_id'] = $adminSchool->id;
            $data['group_school_id'] = $adminSchool->group_school_id;

            $location = village::with('commune.district.province')->findOrFail($data['village_id']);
            $data['province_id'] = $location->commune->district->province_id;
            $data['district_id'] = $location->commune->district_id;
            $data['commune_id'] = $location->commune_id;
        } else {
            SchoolAccess::authorizeSchool($data['school_id']);
            abort_unless(
                School::whereKey($data['school_id'])->value('group_school_id') == $data['group_school_id'],
                403
            );
        }
        if ($schoolId !== null) {
            abort_unless(
                Teacher::where('school_id', $schoolId)->whereIn('id', $data['teacher_id'])->count()
                    === count(array_unique($data['teacher_id'])),
                403
            );
        }

        $teacherIds = $data['teacher_id'];
        unset($data['teacher_id']);

        DB::transaction(function () use ($data, $teacherIds) {
            $censusId = DB::table('census')->insertGetId(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            DB::table('census_teacher')->insert(array_map(fn ($teacherId) => [
                'census_id' => $censusId,
                'teacher_id' => $teacherId,
                'created_at' => now(),
                'updated_at' => now(),
            ], array_unique($teacherIds)));
        });
        return redirect()->route('census.index')->with('status', __('Census created successfully!'));

    }

    private function getSchoolDefaultLocation(School $school): array
    {
        $location = $school->villages->first();

        if (! $location && $school->address) {
            $parts = array_map('trim', explode(',', $school->address));
            if (count($parts) === 4) {
                [$villageName, $communeName, $districtName, $provinceName] = $parts;
                $location = village::with('commune.district.province')->get()->first(fn ($village) =>
                    $village->name === $villageName
                    && $village->commune?->name === $communeName
                    && $village->commune?->district?->name === $districtName
                    && $village->commune?->district?->province?->name === $provinceName
                );
            }
        }

        if (! $location?->commune?->district?->province) {
            return [];
        }

        return [
            'province_id' => $location->commune->district->province_id,
            'district_id' => $location->commune->district_id,
            'commune_id' => $location->commune_id,
            'village_id' => $location->id,
        ];
    }

    private function buildSchoolOptions($schools)
    {
        return $schools->map(function ($school) {
            $village = $school->villages->first();

            return [
                'id' => $school->id,
                'name' => $school->name,
                'address' => $school->address ?? '',
                'group_school_id' => $school->group_school_id,
                'location' => $village?->commune?->district?->province ? [
                    'province_id' => $village->commune->district->province_id,
                    'district_id' => $village->commune->district_id,
                    'commune_id' => $village->commune_id,
                    'village_id' => $village->id,
                ] : null,
            ];
        })->values();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $census = SchoolAccess::restrict(\App\Models\Census::with('year'))->find($id);
        if (!$census) {
            return redirect()->route('census.index')->with('error', __('Census not found.'));
        }
        SchoolAccess::authorizeCensus($census);
        $year = $census->year;
        $groupSchool = GroupSchool::find($census->group_school_id);
        $school = School::find($census->school_id);
        $province = Province::find($census->province_id);
        $district = District::find($census->district_id);
        $commune = commune::find($census->commune_id);
        $village = village::find($census->village_id);
        $teacher = DB::table('census_teacher')
            ->join('teacher', 'census_teacher.teacher_id', '=', 'teacher.id')
            ->where('census_teacher.census_id', $census->id)
            ->when(SchoolAccess::schoolId() !== null, fn ($query) => $query->where('teacher.school_id', SchoolAccess::schoolId()))
            ->orderBy('teacher.name')
            ->pluck('teacher.name');
        return view('census.show', compact(
            'census',
            'year',
            'groupSchool',
            'school',
            'province',
            'district',
            'commune',
            'village',
            'teacher'   
        ));
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $schoolId = SchoolAccess::schoolId();
        $census = DB::table('census')->where('id', $id)
            ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId))
            ->first();

        if (! $census) {
            return redirect()->route('census.index')->with('error', __('Census not found.'));
        }

        $years = Year::where('is_active', true)->orderBy('year')->get();
        $groupSchools = GroupSchool::query()
            ->when($schoolId !== null, fn ($query) => $query->whereHas('schools', fn ($schools) => $schools->where('schools.id', $schoolId)))
            ->orderBy('name')->get();
        $schools = SchoolAccess::schoolQuery()
            ->with('villages.commune.district.province')
            ->orderBy('name')
            ->get();
        $provinces = Province::with('districts.communes.villages')->orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $communes = commune::orderBy('name')->get();
        $villages = village::orderBy('name')->get();
        $teachers = SchoolAccess::teacherQuery()->orderBy('name')->get();
        $teacherIds = DB::table('census_teacher')
            ->join('teacher', 'census_teacher.teacher_id', '=', 'teacher.id')
            ->where('census_teacher.census_id', $id)
            ->when($schoolId !== null, fn ($query) => $query->where('teacher.school_id', $schoolId))
            ->pluck('census_teacher.teacher_id')
            ->all();

        $locationTree = $provinces->map(fn ($province) => [
            'id' => $province->id,
            'name' => $province->name,
            'districts' => $province->districts->map(fn ($district) => [
                'id' => $district->id,
                'name' => $district->name,
                'communes' => $district->communes->map(fn ($commune) => [
                    'id' => $commune->id,
                    'name' => $commune->name,
                    'villages' => $commune->villages->map(fn ($village) => [
                        'id' => $village->id,
                        'name' => $village->name,
                    ])->values(),
                ])->values(),
            ])->values(),
        ])->values();

        $schoolOptions = $this->buildSchoolOptions($schools);

        $groupSchoolOptions = $groupSchools->map(fn ($groupSchool) => [
            'id' => $groupSchool->id,
            'name' => $groupSchool->name,
            'address' => $groupSchool->address ?? '',
        ])->values();

        $teacherOptions = $teachers->map(fn ($teacher) => [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'school_id' => $teacher->school_id,
        ])->values();

        return view('census.edit', compact(
            'census',
            'years',
            'groupSchools',
            'schools',
            'provinces',
            'districts',
            'communes',
            'villages',
            'teachers',
            'teacherIds',
            'locationTree',
            'schoolOptions',
            'groupSchoolOptions',
            'teacherOptions'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schoolId = SchoolAccess::schoolId();
        $existing = DB::table('census')->where('id', $id)
            ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId))
            ->first();
        abort_if(! $existing, 404);
        $data = $request->validate([
            'year_id' => ['required', Rule::exists('year', 'id')->where('is_active', true)],
            'group_school_id' => 'required|exists:group_schools,id',
            'school_id' => 'required|exists:schools,id',
            'province_id' => 'required|exists:province,id',
            'district_id' => 'required|exists:district,id',
            'commune_id' => 'required|exists:commune,id',
            'village_id' => 'required|exists:village,id',
            'teacher_id' => 'required|array|min:1',
            'teacher_id.*' => 'required|exists:teacher,id',
            'note' => 'nullable|string|max:255',
        ]);

        SchoolAccess::authorizeSchool($data['school_id']);
        abort_unless(
            SchoolAccess::schoolId() === null
                || School::whereKey($data['school_id'])->value('group_school_id') == $data['group_school_id'],
            403
        );
        if ($schoolId !== null) {
            abort_unless(
                Teacher::where('school_id', $schoolId)->whereIn('id', $data['teacher_id'])->count()
                    === count(array_unique($data['teacher_id'])),
                403
            );
        }

        $teacherIds = $data['teacher_id'];
        unset($data['teacher_id']);

        DB::transaction(function () use ($id, $data, $teacherIds, $schoolId) {
            $updated = DB::table('census')
                ->where('id', $id)
                ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId))
                ->update(array_merge($data, [
                    'updated_at' => now(),
                ]));
            abort_unless($updated === 1, 404);

            DB::table('census_teacher')->where('census_id', $id)->delete();

            DB::table('census_teacher')->insert(array_map(fn ($teacherId) => [
                'census_id' => $id,
                'teacher_id' => $teacherId,
                'created_at' => now(),
                'updated_at' => now(),
            ], array_unique($teacherIds)));
        });

        return redirect()->route('census.index')->with('status', __('Census updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schoolId = SchoolAccess::schoolId();
        abort_unless(
            DB::table('census')->where('id', $id)
                ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId))
                ->exists(),
            404
        );
        DB::transaction(function () use ($id, $schoolId) {
            $deleted = DB::table('census')->where('id', $id)
                ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId))
                ->delete();
            abort_unless($deleted === 1, 404);
            DB::table('census_teacher')->where('census_id', $id)->delete();
        });

        return redirect()->route('census.index')->with('status', __('Census deleted successfully!'));
    }
}
