<?php

namespace App\Http\Controllers;

use App\Models\GroupSchool;
use App\Models\Province;
use App\Models\commune;
use App\Models\village;
use Illuminate\Http\Request;

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
        $schools = \App\Models\School::with('groupSchool')->get();
        return view('schools.index', compact('schools'));
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $locations = Province::with('districts.communes.villages')->orderBy('name')->get();
        $groupSchools = GroupSchool::orderBy('name')->get();
        $groupSchoolOptions = $groupSchools->map(fn (GroupSchool $groupSchool) => [
            'id' => $groupSchool->id,
            'name' => $groupSchool->name,
            'address' => $groupSchool->address ?? '',
        ])->values();

        return view('schools.create', compact('groupSchools', 'groupSchoolOptions', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'director_name' => ['nullable', 'string', 'max:255'],
            'province_id' => ['nullable', 'exists:province,id'],
            'district_id' => ['nullable', 'exists:district,id'],
            'commune_id' => ['nullable', 'exists:commune,id'],
            'village_id' => ['nullable', 'exists:village,id'],
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

        if ($request->filled('village_id')) {
            $data['address'] = $this->buildLocationAddress($request->village_id) ?? $data['address'];
        }

        \App\Models\School::create($data);
        return redirect()->route('schools.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $school = \App\Models\School::with(['groupSchool', 'teachers'])->findOrFail($id);
        return view('schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $school = \App\Models\School::findOrFail($id);
        $groupSchools = \App\Models\GroupSchool::orderBy('name')->get();
        $locations = Province::with('districts.communes.villages')->orderBy('name')->get();
        $selectedLocation = $this->findLocationFromAddress($school->address);

        return view('schools.update', compact('school', 'groupSchools', 'locations', 'selectedLocation'));
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
            'district_id' => ['required', 'exists:district,id'],
            'commune_id' => ['required', 'exists:commune,id'],
            'village_id' => ['required', 'exists:village,id'],
            'group_school_id' => ['required', 'exists:group_schools,id'],
        ]);

        $data['address'] = $this->buildLocationAddress($request->village_id);
        unset($data['province_id'], $data['district_id'], $data['commune_id'], $data['village_id']);

        $school->update($data);
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
