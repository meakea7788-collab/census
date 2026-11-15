<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class YearController extends Controller
{
    //
    public function index()
    {
        $years = \App\Models\Year::orderByDesc('year')->get();

        return view('years.index', compact('years'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->role === User::ROLE_SUPERADMIN, 403);

        return redirect()->route('years.index');
    }

    function store(Request $request)
    {
        abort_unless(auth()->user()?->role === User::ROLE_SUPERADMIN, 403);

        $data = $request->validate([
            'year' => ['required', 'integer', 'min:1900', 'max:2100', Rule::unique('year', 'year')],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        \App\Models\Year::create($data);

        return redirect()->route('years.index')->with('status', __('Year created successfully!'));
    }
    function destroy($id)
    {
        $year = \App\Models\Year::findOrFail($id);
        $year->delete();

        return redirect()->route('years.index')->with('status', __('Year deleted successfully!'));
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
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $year->update($data);
        return redirect()->route('years.index')->with('status', __('Year updated successfully!'));
    }
}
