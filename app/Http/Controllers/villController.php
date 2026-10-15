<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\commune;
use App\Models\village;

class villController extends Controller
{
    //
    function index()
    {
        $villages = village::all();
        return view('address.village.index', compact('villages'));
    }
    function create()
    {
        return view('address.village.create');  
    }

    public function createForCommune(commune $commune)
    {
        return view('address.village.create', compact('commune'));
    }

    public function storeForCommune(Request $request, commune $commune)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $commune->villages()->firstOrCreate(['name' => $data['name']]);

        return redirect()
            ->route('communes.show', $commune)
            ->with('success', 'Village saved. An existing village with this name was reused if present.');
    }

    function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'commune_id' => ['required', 'exists:commune,id'],       
        ]);
        // Store the village in the database
        village::firstOrCreate([
            'name' => $data['name'],
            'commune_id' => $data['commune_id'],
        ]);

        return redirect()
            ->route('communes.show', $data['commune_id'])
            ->with('success', 'Village saved.');
    }
      function edit($id)
    {
        $village = village::findOrFail($id);
        return view('address.village.edit', compact('village'));        
    }
    function update(Request $request, $id)
    {
        $village = village::findOrFail($id);        
    }
    function destroy($id)
    {
        $village = village::findOrFail($id);
        $village->delete();
        return redirect()->route('address.village.index')->with('success', 'Village deleted successfully.');
    }
}
