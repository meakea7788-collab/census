<?php

namespace App\Http\Controllers;

use App\Models\GroupSchool;
use Illuminate\Http\Request;

class GroupSchoolController extends Controller
{
    public function create()
    {
        return view('group-school.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'director_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:1000'],
        ]);

        GroupSchool::create($data);

        return redirect()
            ->route('group-school.create')
            ->with('status', 'Group school created successfully.');
    }
}
