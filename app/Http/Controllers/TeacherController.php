<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    //
    public function index()
    {
        $teachers = Teacher::with('school')->orderBy('name')->get();

        return view('teachers.index', compact('teachers'));
    }
    public function create(Request $request)
    {
        $schools = School::orderBy('name')->get();
        $selectedSchoolId = $request->filled('school_id')
            ? School::findOrFail($request->input('school_id'))->id
            : null;

        return view('teachers.create', compact('schools', 'selectedSchoolId'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'required|string|max:10',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'school_id' => 'required|exists:schools,id',
        ]);

        $teacher = Teacher::create($data);

        return redirect()
            ->route('schools.show', $teacher->school_id)
            ->with('success', __('Teacher created successfully.'));
    }
    public function show(Teacher $teacher)
    {
        return view('teachers.show', compact('teacher'));
    }
    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', compact('teacher'));   
    }
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sex' => 'required|string|max:10',
            'position' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'school_id' => 'required|exists:schools,id',
        ]);

        $teacher->update($request->all());

        return redirect()->route('teachers.index')->with('success', __('Teacher updated successfully.'));
    }
    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('teachers.index')->with('success', __('Teacher deleted successfully.'));
    }   
}
