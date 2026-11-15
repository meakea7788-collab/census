<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\family as Family;
use App\Models\hole_house;
use App\Support\SchoolAccess;

class FamilyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $families = $this->familiesQuery()->with('holehouse')->latest()->get();

        return view('family.index', compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $holeHouses = $this->holeHousesQuery()->orderBy('hole_number')->get();
        $selectedHoleHouse = $request->filled('holehouse_id')
            ? $this->holeHousesQuery()->findOrFail($request->query('holehouse_id'))
            : null;

        return view('family.create', compact('holeHouses', 'selectedHoleHouse'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'holehouse_id' => ['required', 'exists:holehouse,id'],
            'family_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'relationship' => ['required', 'string', 'max:255'],
            'job' => ['required', 'string', 'max:255'],
        ]);
        $this->holeHousesQuery()->findOrFail($data['holehouse_id']);

        Family::create($data);

        return redirect()->route('family.index')->with('success', __('Family created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $family = $this->familiesQuery()->with('holehouse.families')->findOrFail($id);

        return view('family.show', compact('family'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $family = $this->familiesQuery()->findOrFail($id);
        $holeHouses = $this->holeHousesQuery()->orderBy('hole_number')->get();

        return view('family.edit', compact('family', 'holeHouses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data = $request->validate([
            'holehouse_id' => ['required', 'exists:holehouse,id'],
            'family_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'relationship' => ['required', 'string', 'max:255'],
            'job' => ['required', 'string', 'max:255'],
        ]);

        $family = $this->familiesQuery()->findOrFail($id);
        $this->holeHousesQuery()->findOrFail($data['holehouse_id']);
        $family->update($data);

        return redirect()->route('family.index')->with('success', 'Family updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $family = $this->familiesQuery()->findOrFail($id);
        $family->delete();

        return redirect()->route('family.index')->with('success', __('Family member deleted successfully.'));
    }

    public function editMoveAddress(string $id)
    {
        $family = $this->familiesQuery()->with('holehouse')->findOrFail($id);

        if ($family->is_deceased) {
            return redirect()->route('family.index')->with('error', __('A deceased family member cannot be marked as moved.'));
        }

        return view('family.move', compact('family'));
    }

    public function updateMoveAddress(Request $request, string $id)
    {
        $family = $this->familiesQuery()->findOrFail($id);

        if ($family->is_deceased) {
            return redirect()->route('family.index')->with('error', __('A deceased family member cannot be marked as moved.'));
        }

        $data = $request->validate([
            'moved_to_address' => ['required', 'string', 'max:2000'],
        ]);

        $family->update($data);

        return redirect()->route('family.index')->with('success', __('Family member move address saved.'));
    }

    public function markDeceased(string $id)
    {
        $family = $this->familiesQuery()->findOrFail($id);

        if (! $family->is_deceased) {
            $family->update(['is_deceased' => true]);
        }

        return redirect()->route('family.index')->with('success', __('Family member marked as deceased.'));
    }

    private function familiesQuery()
    {
        $schoolId = SchoolAccess::schoolId();

        return Family::query()->whereHas('holehouse.census', fn ($query) => $query
            ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId)));
    }

    private function holeHousesQuery()
    {
        $schoolId = SchoolAccess::schoolId();

        return hole_house::query()->whereHas('census', fn ($query) => $query
            ->when($schoolId !== null, fn ($query) => $query->where('school_id', $schoolId)));
    }
}
