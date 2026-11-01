<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class YearController extends Controller
{
    //
    public function index()
    {
        $years = \App\Models\Year::all();
        return view('years.index', compact('years'));
    }
    public function create()
    {
        return view('years.create');
    }   
    function store(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:1900', 'max:2100', Rule::unique('year', 'year')],
        ]);

        \App\Models\Year::create($data);

        return redirect()->route('years.index')->with('status', 'Year created successfully!');
    }
    function destroy($id)
    {
        $year = \App\Models\Year::findOrFail($id);
        $year->delete();

        return redirect()->route('years.index')->with('status', 'Year deleted successfully!');
    }
    function edit($id)
    {
        $year = \App\Models\Year::findOrFail($id);
        return view('years.edit', compact('year'));
    }
    function update(Request $request, $id)
    {
        $year = \App\Models\Year::findOrFail($id);
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:1900', 'max:2100', Rule::unique('year', 'year')->ignore($year->id)],
        ]);
        $year->update($data);
        return redirect()->route('years.index')->with('status', 'Year updated successfully!');
    }
}
