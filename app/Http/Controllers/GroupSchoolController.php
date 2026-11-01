<?php

namespace App\Http\Controllers;

use App\Models\GroupSchool;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GroupSchoolController extends Controller
{
    public function create()
    {
        $provinces = Province::with('districts.communes.villages')
            ->orderBy('name')
            ->get();
        $locations = $provinces->map(fn ($province) => [
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

        return view('group-school.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'director_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'province_id' => ['required', 'exists:province,id'],
            'district_id' => [
                'required',
                Rule::exists('district', 'id')->where('province_id', $request->input('province_id')),
            ],
            'commune_id' => [
                'required',
                Rule::exists('commune', 'id')->where('district_id', $request->input('district_id')),
            ],
            'village_id' => [
                'required',
                Rule::exists('village', 'id')->where('commune_id', $request->input('commune_id')),
            ],
        ]);

        $village = \App\Models\village::with('commune.district.province')->findOrFail($data['village_id']);
        $data['address'] = implode(', ', [
            $village->name,
            $village->commune->name,
            $village->commune->district->name,
            $village->commune->district->province->name,
        ]);

        unset($data['province_id'], $data['district_id'], $data['commune_id'], $data['village_id']);

        GroupSchool::create($data);

        return redirect()
            ->route('group-school.create')
            ->with('status', 'Group school created successfully.');
    }
    public function index()
    {
        $groupSchools = GroupSchool::all();
        return view('group-school.index', compact('groupSchools'));
    }
    public function show(GroupSchool $groupSchool)
    {
        $groupSchool->load('schools');

        return view('group-school.show', compact('groupSchool'));
    }

    public function destroy(GroupSchool $groupSchool)
    {
        $groupSchool->delete();

        return redirect()
            ->route('group-school.index')
            ->with('status', 'Group school deleted successfully.');
    }
    public function edit(GroupSchool $groupSchool)
    {
        $locations = $this->getLocations();

        $selectedLocation = $this->findLocationFromAddress($groupSchool->address);

        return view('group-school.edit', compact('groupSchool', 'locations', 'selectedLocation'));
    }

    public function update(Request $request, GroupSchool $groupSchool)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'director_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'province_id' => ['required', 'exists:province,id'],
            'district_id' => [
                'required',
                Rule::exists('district', 'id')->where('province_id', $request->input('province_id')),
            ],
            'commune_id' => [
                'required',
                Rule::exists('commune', 'id')->where('district_id', $request->input('district_id')),
            ],
            'village_id' => [
                'required',
                Rule::exists('village', 'id')->where('commune_id', $request->input('commune_id')),
            ],
        ]);

        $village = \App\Models\village::with('commune.district.province')->findOrFail($data['village_id']);
        $data['address'] = implode(', ', [
            $village->name,
            $village->commune->name,
            $village->commune->district->name,
            $village->commune->district->province->name,
        ]);

        unset($data['province_id'], $data['district_id'], $data['commune_id'], $data['village_id']);

        $groupSchool->update($data);

        return redirect()
            ->route('group-school.index')
            ->with('status', 'Group school updated successfully.');
    }

    private function getLocations()
    {
        $provinces = Province::with('districts.communes.villages')
            ->orderBy('name')
            ->get();

        return $provinces->map(fn ($province) => [
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
    }

    private function findLocationFromAddress(?string $address): array
    {
        $parts = array_map('trim', explode(',', $address ?? ''));

        if (count($parts) !== 4) {
            return [];
        }

        [$villageName, $communeName, $districtName, $provinceName] = $parts;

        $village = \App\Models\village::with('commune.district.province')->get()
            ->first(fn ($item) =>
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
}
