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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CensusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $censusRecords = DB::table('census')
            ->join('year', 'census.year_id', '=', 'year.id')
            ->join('schools', 'census.school_id', '=', 'schools.id')
            ->join('village', 'census.village_id', '=', 'village.id')
            ->select(
                'census.*',
                'year.year',
                'schools.name as school_name',
                'village.name as village_name'
            )
            ->orderByDesc('census.id')
            ->get();

        $teacherNames = DB::table('census_teacher')
            ->join('teacher', 'census_teacher.teacher_id', '=', 'teacher.id')
            ->select('census_teacher.census_id', 'teacher.name')
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
        $years = Year::orderBy('year')->get();
        $groupSchools = GroupSchool::orderBy('name')->get();
        $schools = School::orderBy('name')->get();
        $provinces = Province::with('districts.communes.villages')->orderBy('name')->get();
        $districts = District::orderBy('name')->get();
        $communes = commune::orderBy('name')->get();
        $villages = village::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();

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

        $schoolOptions = $schools->map(fn ($school) => [
            'id' => $school->id,
            'name' => $school->name,
            'address' => $school->address ?? '',
            'group_school_id' => $school->group_school_id,
        ])->values();

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
            'teacherOptions'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'year_id' => 'required|exists:table_tbl_year,id',
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
        return redirect()->route('census.index')->with('status', 'Census created successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $census = DB::table('census')->where('id', $id)->first();
        if (!$census) {
            return redirect()->route('census.index')->with('error', 'Census not found.');
        }
        $year = Year::find($census->year_id);
        $groupSchool = GroupSchool::find($census->group_school_id);
        $school = School::find($census->school_id);
        $province = Province::find($census->province_id);
        $district = District::find($census->district_id);
        $commune = commune::find($census->commune_id);
        $village = village::find($census->village_id);
        $teacher = DB::table('census_teacher')
            ->join('teacher', 'census_teacher.teacher_id', '=', 'teacher.id')
            ->where('census_teacher.census_id', $census->id)
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
