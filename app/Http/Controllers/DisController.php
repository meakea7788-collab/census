<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;

class DisController extends Controller
{
    //
    public function index()
    {
        return view('address.district.index');
    }
    public function create()
    {
        return view('address.district.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:province,id'],
        ]);

        // Store the district in the database
        District::create($data);

        return redirect()->route('district.index')->with('success', 'District created successfully.');
    }
    public function edit($id)
    {
        $district = District::findOrFail($id);
        return view('address.district.edit', compact('district'));
    }
    public function update(Request $request, $id)
    {
        $district = District::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:province,id'],
        ]);

        // Update the district in the database
        $district->update($data);

        return redirect()->route('address.district.index')->with('success', 'District updated successfully.');
    }
    public function destroy($id)
    {
        $district = District::findOrFail($id);
        $district->delete();
        return redirect()->route('address.district.index')->with('success', 'District deleted successfully.');
    }
}
