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
    public function destroy(GroupSchool $groupSchool)
    {
        $groupSchool->delete();

        return redirect()
            ->route('group-school.index')
            ->with('status', 'Group school deleted successfully.');
    }
    public function edit(GroupSchool $groupSchool)
    {
        return view('group-school.edit', compact('groupSchool'));
    }

    public function update(Request $request, GroupSchool $groupSchool)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'director_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:1000'],
        ]);

        $groupSchool->update($data);

        return redirect()
            ->route('group-school.index')
            ->with('status', 'Group school updated successfully.');
    }
}
