<?php

namespace App\Http\Controllers;

use App\Models\GroupSchool;
use App\Models\Province;
use App\Models\commune;
use App\Models\village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    private function buildLocationAddress(?string $villageId): ?string
    {
        if (empty($villageId)) {
            return null;
        }

        $village = village::with('commune.district.province')->find($villageId);

        if (! $village) {
            return null;
        }

        return implode(', ', [
            $village->name,
            $village->commune?->name,
            $village->commune?->district?->name,
            $village->commune?->district?->province?->name,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = \App\Models\School::with(['groupSchool', 'villages'])->get();
        return view('schools.index', compact('schools'));
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $locations = Province::with('districts.communes.villages')->orderBy('name')->get();
        $groupSchools = GroupSchool::orderBy('name')->get();
        $selectedGroupSchool = $request->filled('group_school_id')
            ? GroupSchool::findOrFail($request->input('group_school_id'))
            : null;
        $selectedLocation = $this->findLocationFromAddress($selectedGroupSchool?->address);
        $groupSchoolOptions = $groupSchools->map(fn (GroupSchool $groupSchool) => [
            'id' => $groupSchool->id,
            'name' => $groupSchool->name,
            'address' => $groupSchool->address ?? '',
        ])->values();

        return view('schools.create', compact(
            'groupSchools',
            'groupSchoolOptions',
            'locations',
            'selectedGroupSchool',
            'selectedLocation'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'province_id' => ['required', 'exists:province,id'],
            'district_id' => [
                'required',
                Rule::exists('district', 'id')->where('province_id', $request->input('province_id')),
            ],
            'commune_id' => [
                'required',
                Rule::exists('commune', 'id')->where('district_id', $request->input('district_id')),
            ],
            'village_ids' => ['required', 'array', 'min:1'],
            'village_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('village', 'id')->where('commune_id', $request->input('commune_id')),
            ],
            'group_school_id' => ['required', 'exists:group_schools,id'],
        ]);

        $selectedCommune = $request->filled('commune_id') ? commune::find($request->commune_id) : null;
        $selectedGroupSchool = GroupSchool::find($request->group_school_id);

        if ($selectedCommune && $selectedGroupSchool) {
            $groupSchoolAddress = strtolower((string) ($selectedGroupSchool->address ?? ''));
            $communeName = strtolower((string) $selectedCommune->name);

            if (! str_contains($groupSchoolAddress, $communeName)) {
                return back()
                    ->withErrors(['group_school_id' => 'This group school is not in the selected commune.'])
                    ->withInput();
            }
        }

        $selectedVillages = village::with('commune.district.province')
            ->whereIn('id', $data['village_ids'])
            ->get();
        $primaryVillage = $selectedVillages->first();
        $data['address'] = $this->buildLocationAddress((string) $primaryVillage->id) ?? $data['address'];
        $data['village'] = $primaryVillage->name;
        $villageIds = $selectedVillages->modelKeys();
        unset($data['province_id'], $data['district_id'], $data['commune_id'], $data['village_ids']);

        DB::transaction(function () use ($data, $villageIds) {
            $school = \App\Models\School::create($data);
            $school->villages()->sync($villageIds);
        });
        return redirect()->route('schools.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $school = \App\Models\School::with(['groupSchool', 'teachers', 'villages'])->findOrFail($id);
        return view('schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $school = \App\Models\School::with('villages')->findOrFail($id);
        $groupSchools = \App\Models\GroupSchool::orderBy('name')->get();
        $locations = Province::with('districts.communes.villages')->orderBy('name')->get();
        $selectedLocation = $this->findLocationFromAddress($school->address);
        $selectedVillageIds = $school->villages->isNotEmpty()
            ? $school->villages->modelKeys()
            : (isset($selectedLocation['village']) ? [$selectedLocation['village']] : []);

        return view('schools.update', compact(
            'school',
            'groupSchools',
            'locations',
            'selectedLocation',
            'selectedVillageIds'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $school = \App\Models\School::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'province_id' => ['required', 'exists:province,id'],
            'district_id' => [
                'required',
                Rule::exists('district', 'id')->where('province_id', $request->input('province_id')),
            ],
            'commune_id' => [
                'required',
                Rule::exists('commune', 'id')->where('district_id', $request->input('district_id')),
            ],
            'village_ids' => ['required', 'array', 'min:1'],
            'village_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('village', 'id')->where('commune_id', $request->input('commune_id')),
            ],
            'group_school_id' => ['required', 'exists:group_schools,id'],
        ]);

        $selectedCommune = commune::findOrFail($data['commune_id']);
        $selectedGroupSchool = GroupSchool::findOrFail($data['group_school_id']);
        if (! str_contains(
            strtolower((string) $selectedGroupSchool->address),
            strtolower($selectedCommune->name)
        )) {
            return back()
                ->withErrors(['group_school_id' => 'This group school is not in the selected commune.'])
                ->withInput();
        }

        $selectedVillages = village::with('commune.district.province')
            ->whereIn('id', $data['village_ids'])
            ->get();
        $primaryVillage = $selectedVillages->first();
        $data['address'] = $this->buildLocationAddress((string) $primaryVillage->id);
        $data['village'] = $primaryVillage->name;
        $villageIds = $selectedVillages->modelKeys();
        unset($data['province_id'], $data['district_id'], $data['commune_id'], $data['village_ids']);

        DB::transaction(function () use ($school, $data, $villageIds) {
            $school->update($data);
            $school->villages()->sync($villageIds);
        });
        return redirect()->route('schools.index');
    }

    private function findLocationFromAddress(?string $address): array
    {
        $parts = array_map('trim', explode(',', $address ?? ''));

        if (count($parts) !== 4) {
            return [];
        }

        [$villageName, $communeName, $districtName, $provinceName] = $parts;
        $village = village::with('commune.district.province')->get()->first(fn ($item) =>
            $item->name === $villageName
            && $item->commune?->name === $communeName
            && $item->commune?->district?->name === $districtName
            && $item->commune?->district?->province?->name === $provinceName
        );

        if (! $village) {
            return [];
        }

        return [
            'province' => $village->commune->district->province->id,
            'district' => $village->commune->district->id,
            'commune' => $village->commune->id,
            'village' => $village->id,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $school = \App\Models\School::findOrFail($id);
        $school->delete();
        return redirect()->route('schools.index');

    }
}
