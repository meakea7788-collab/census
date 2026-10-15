<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\commune;
use App\Models\District;

class ComController extends Controller
{
    //
    function index()
    {
        return view('address.commune.index');
    }
    function create()
    {
        return view('address.commune.create');
    }

    public function showDistrict(District $district)
    {
        $district->load('communes');

        return view('address.district.show', compact('district'));
    }

    public function createForDistrict(District $district)
    {
        return view('address.commune.create', compact('district'));
    }

    public function storeForDistrict(Request $request, District $district)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $district->communes()->firstOrCreate(['name' => $data['name']]);

        return redirect()
            ->route('districts.communes.show', $district)
            ->with('success', 'Commune saved. An existing commune with this name was reused if present.');
    }

    public function showCommune(commune $commune)
    {
        $commune->load('villages');

        return view('address.commune.show', compact('commune'));
    }

    public function editCommune(commune $commune)
    {
        return view('address.commune.edit', compact('commune'));
    }

    public function updateCommune(Request $request, commune $commune)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $commune->update($data);

        return redirect()
            ->route('districts.communes.show', $commune->district_id)
            ->with('success', 'Commune updated successfully.');
    }

    public function destroyCommune(commune $commune)
    {
        $districtId = $commune->district_id;

        $commune->villages()->delete();
        $commune->delete();

        return redirect()
            ->route('districts.communes.show', $districtId)
            ->with('success', 'Commune and its villages deleted successfully.');
    }

    function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'district_id' => ['required', 'exists:district,id'],       
        ]);
        // Store the commune in the database
        commune::firstOrCreate([
            'name' => $data['name'],
            'district_id' => $data['district_id'],
        ]);

        return redirect()
            ->route('districts.communes.show', $data['district_id'])
            ->with('success', 'Commune saved.');
    }
      function edit($id)
    {
        $commune = commune::findOrFail($id);
        return view('address.commune.edit', compact('commune'));        
    }
    function update(Request $request, $id)
    {
        $commune = commune::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'district_id' => ['required', 'exists:district,id'],       
        ]);

        // Update the commune in the database
        $commune->update($data);

        return redirect()->route('address.commune.index')->with('success', 'Commune updated successfully.');
    }       
    function destroy($id)
    {
        $commune = commune::findOrFail($id);
        $commune->delete();
        return redirect()->route('address.commune.index')->with('success', 'Commune deleted successfully.');
    }   

}
