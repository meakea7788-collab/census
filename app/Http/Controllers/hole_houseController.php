<?php

namespace App\Http\Controllers;

use App\Models\hole_house;
use App\Models\family as Family;
use App\Models\Census;
use App\Support\SchoolAccess;
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
        $villageId = trim((string) $request->query('village_id', ''));
        $yearId = trim((string) $request->query('year_id', ''));
        $censusId = trim((string) $request->query('census_id', ''));

        // If census_id is provided but not year_id, resolve year_id from that census
        if ($censusId !== '' && $yearId === '') {
            $matchedCensus = DB::table('census')->where('id', $censusId)->first();
            if ($matchedCensus) {
                $yearId = (string) $matchedCensus->year_id;
            }
        }

        // Normalize yearId if passed as a 4-digit year number (e.g. 2026)
        if ($yearId !== '') {
            $yearRecord = DB::table('year')
                ->where('id', $yearId)
                ->orWhere('year', $yearId)
                ->first();
            if ($yearRecord) {
                $yearId = (string) $yearRecord->id;
            }
        }

        // Get active years (including the selected year if inactive)
        $years = \App\Models\Year::query()
            ->where(function ($query) use ($yearId) {
                $query->where('is_active', true);
                if ($yearId !== '') {
                    $query->orWhere('id', $yearId);
                }
            })
            ->orderByDesc('year')
            ->get();

        $yearCensusIds = [];
        if ($yearId !== '') {
            $yearCensusIds = SchoolAccess::censusQuery()
                ->where('census.year_id', $yearId)
                ->pluck('census.id')
                ->all();
        }

        $villages = SchoolAccess::accessibleVillages();
        if ($yearId !== '' && ! empty($yearCensusIds)) {
            $communeIds = DB::table('census')
                ->whereIn('id', $yearCensusIds)
                ->pluck('commune_id')
                ->filter()
                ->unique()
                ->all();

            $villageIds = DB::table('census')
                ->whereIn('id', $yearCensusIds)
                ->whereNotNull('village_id')
                ->pluck('village_id')
                ->merge(
                    DB::table('holehouse')
                        ->whereIn('census_id', $yearCensusIds)
                        ->whereNotNull('village_id')
                        ->pluck('village_id')
                )
                ->filter()
                ->unique()
                ->all();

            if (! empty($villageIds)) {
                $villages = $villages->whereIn('id', $villageIds)->values();
            } elseif (! empty($communeIds)) {
                $communeVillageIds = DB::table('village')
                    ->whereIn('commune_id', $communeIds)
                    ->pluck('id');
                $villages = $villages->whereIn('id', $communeVillageIds)->values();
            }
        }

        $holeHouses = SchoolAccess::holeHousesQuery()
            ->with(['village', 'families'])
            ->when($yearId !== '', function ($query) use ($yearCensusIds) {
                $query->whereIn('holehouse.census_id', $yearCensusIds);
            })
            ->when($villageId !== '', function ($query) use ($villageId) {
                $query->where('holehouse.village_id', $villageId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('leader_family_name', 'like', '%' . $search . '%')
                        ->orWhere('hole_number', 'like', '%' . $search . '%')
                        ->orWhereHas('village', function ($villageQuery) use ($search) {
                            $villageQuery->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('families', function ($familyQuery) use ($search) {
                            $familyQuery->where('family_name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('id')
            ->get();

        return view('hole_houses.index', compact('holeHouses', 'search', 'villages', 'villageId', 'years', 'yearId', 'censusId'));
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
            'census_id' => [
                'required',
                Rule::exists('census', 'id')->where(function ($query) {
                    $query->whereIn('year_id', DB::table('year')->where('is_active', true)->select('id'));
                }),
            ],
        ]);
        SchoolAccess::censusQuery()->findOrFail($validatedCensus['census_id']);
        $communeId = DB::table('census')->where('id', $validatedCensus['census_id'])->value('commune_id');

        $data = $request->validate([
            'hole_number'        => ['required', 'integer', 'min:1'],
            'leader_family_name' => ['required', 'string', 'max:255'],
            'phone_number'       => ['required', 'string', 'max:255'],
            'address'            => ['nullable', 'string', 'max:2000'],
            'village_id'         => ['required', Rule::exists('village', 'id')->where('commune_id', $communeId)],
            'status'             => ['required', 'string', 'max:255'],
            // Leader as family member fields
            'leader_gender'      => ['required', 'string', 'max:255'],
            'leader_dob'         => ['required', 'date'],
            'leader_job'         => ['required', 'string', 'max:255'],
        ]);
        $data['census_id'] = $validatedCensus['census_id'];

        // Extract leader family fields before creating house
        $leaderGender = $data['leader_gender'];
        $leaderDob    = $data['leader_dob'];
        $leaderJob    = $data['leader_job'];
        unset($data['leader_gender'], $data['leader_dob'], $data['leader_job']);

        $holeHouse = hole_house::create($data);

        // Auto-create the household leader as a family member
        Family::create([
            'holehouse_id'  => $holeHouse->id,
            'family_name'   => $holeHouse->leader_family_name,
            'gender'        => $leaderGender,
            'date_of_birth' => $leaderDob,
            'relationship'  => 'មេគ្រួសារ',
            'job'           => $leaderJob,
        ]);

        return redirect()->route('hole_houses.show', $holeHouse->id)->with('success', __('Hole House created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $holeHouse = SchoolAccess::holeHousesQuery()
            ->with(['families', 'village'])
            ->findOrFail($id);

        return view('hole_houses.show', compact('holeHouse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $holeHouse = SchoolAccess::holeHousesQuery()->findOrFail($id);
        [$censuses, $villages] = $this->getLocationOptions($holeHouse->census_id);

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
        SchoolAccess::censusQuery()->findOrFail($validatedCensus['census_id']);
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

        $holeHouse = SchoolAccess::holeHousesQuery()->findOrFail($id);
        $holeHouse->update($data);

        return redirect()->route('hole_houses.index')->with('success', __('Hole House updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $holeHouse = SchoolAccess::holeHousesQuery()->findOrFail($id);
        $holeHouse->delete();

        return redirect()->route('hole_houses.index')->with('success', __('Hole House deleted successfully.'));
    }

    private function getLocationOptions(?int $currentCensusId = null): array
    {
        $censuses = SchoolAccess::censusQuery()
            ->join('year', 'census.year_id', '=', 'year.id')
            ->leftJoin('commune', 'census.commune_id', '=', 'commune.id')
            ->where(function ($query) use ($currentCensusId) {
                $query->where('year.is_active', true);
                if ($currentCensusId) {
                    $query->orWhere('census.id', $currentCensusId);
                }
            })
            ->select('census.id', 'census.commune_id', 'census.village_id', 'year.year', 'year.description as year_description', 'commune.name as commune_name')
            ->orderByDesc('year.year')
            ->orderBy('commune.name')
            ->get();

        $communeIds = $censuses->pluck('commune_id')->filter()->unique()->values()->all();

        $villages = DB::table('village')
            ->join('commune', 'village.commune_id', '=', 'commune.id')
            ->select('village.id', 'village.name', 'village.commune_id')
            ->when(! empty($communeIds), fn ($query) => $query->whereIn('village.commune_id', $communeIds))
            ->orderBy('village.name')
            ->get();

        return [$censuses, $villages];
    }
}
