<?php

namespace App\Http\Controllers;

use App\Models\hole_house;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class hole_houseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $holeHouses = hole_house::with('village')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('leader_family_name', 'like', '%' . $search . '%')
                        ->orWhere('hole_number', 'like', '%' . $search . '%')
                        ->orWhereHas('village', function ($villageQuery) use ($search) {
                            $villageQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('id')
            ->get();

        return view('hole_houses.index', compact('holeHouses', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$censuses, $villages] = $this->getLocationOptions();

        return view('hole_houses.create', compact('censuses', 'villages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedCensus = $request->validate([
            'census_id' => ['required', 'exists:census,id'],
        ]);
        $communeId = DB::table('census')->where('id', $validatedCensus['census_id'])->value('commune_id');

        $data = $request->validate([
            'hole_number' => ['required', 'integer', 'min:1'],
            'leader_family_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'village_id' => ['required', Rule::exists('village', 'id')->where('commune_id', $communeId)],
            'status' => ['required', 'string', 'max:255'],
        ]);
        $data['census_id'] = $validatedCensus['census_id'];

        hole_house::create($data);

        return redirect()->route('hole_houses.index')->with('success', __('Hole House created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $holeHouse = hole_house::with(['families', 'village'])->findOrFail($id);

        return view('hole_houses.show', compact('holeHouse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $holeHouse = hole_house::findOrFail($id);
        [$censuses, $villages] = $this->getLocationOptions();

        return view('hole_houses.edit', compact('holeHouse', 'censuses', 'villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedCensus = $request->validate([
            'census_id' => ['required', 'exists:census,id'],
        ]);
        $communeId = DB::table('census')->where('id', $validatedCensus['census_id'])->value('commune_id');

        $data = $request->validate([
            'hole_number' => ['required', 'integer', 'min:1'],
            'leader_family_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'village_id' => ['required', Rule::exists('village', 'id')->where('commune_id', $communeId)],
            'status' => ['required', 'string', 'max:255'],
        ]);
        $data['census_id'] = $validatedCensus['census_id'];

        $holeHouse = hole_house::findOrFail($id);
        $holeHouse->update($data);

        return redirect()->route('hole_houses.index')->with('success', __('Hole House updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $holeHouse = hole_house::findOrFail($id);
        $holeHouse->delete();

        return redirect()->route('hole_houses.index')->with('success', __('Hole House deleted successfully.'));
    }

    private function getLocationOptions(): array
    {
        $censuses = DB::table('census')
            ->leftJoin('year', 'census.year_id', '=', 'year.id')
            ->leftJoin('commune', 'census.commune_id', '=', 'commune.id')
            ->select('census.id', 'census.name', 'census.commune_id', 'year.year', 'commune.name as commune_name')
            ->orderByDesc('year.year')
            ->orderBy('census.name')
            ->get();

        $villages = DB::table('village')
            ->join('commune', 'village.commune_id', '=', 'commune.id')
            ->select('village.id', 'village.name', 'village.commune_id')
            ->orderBy('village.name')
            ->get();

        return [$censuses, $villages];
    }
}
