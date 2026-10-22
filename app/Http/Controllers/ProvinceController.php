<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\commune;
use App\Models\village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProvinceController extends Controller
{
    private function findOrCreateByName(string $table, string $name, array $extra = []): mixed
    {
        $normalized = trim($name);

        $query = DB::table($table)->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($normalized)]);
        $existingRows = $query->orderBy('id')->get();

        if ($existingRows->isNotEmpty()) {
            $primaryId = (int) $existingRows->first()->id;
            $duplicateIds = $existingRows->pluck('id')->reject(fn ($id) => (int) $id === $primaryId)->all();

            if (! empty($duplicateIds)) {
                DB::table($table)->whereIn('id', $duplicateIds)->delete();
            }

            foreach ($extra as $column => $value) {
                $existingValue = DB::table($table)->where('id', $primaryId)->value($column);
                if ($existingValue !== $value) {
                    DB::table($table)->where('id', $primaryId)->update($extra);
                }
            }

            return $primaryId;
        }

        return (int) DB::table($table)->insertGetId(array_merge([
            'name' => $normalized,
            'created_at' => now(),
            'updated_at' => now(),
        ], $extra));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $provinces = Province::orderBy('name')->get();

        return view('address.province.index', compact('provinces'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('address.province.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'province_name' => ['required', 'string', 'max:255'],
            'district_name' => ['required', 'string', 'max:255'],
            'commune_name' => ['required', 'string', 'max:255'],
            'village_name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data): void {
            $provinceId = $this->findOrCreateByName('province', $data['province_name']);
            $province = Province::findOrFail($provinceId);

            $districtId = $this->findOrCreateByName('district', $data['district_name'], ['province_id' => $province->id]);
            $district = District::findOrFail($districtId);

            $communeId = $this->findOrCreateByName('commune', $data['commune_name'], ['district_id' => $district->id]);
            $commune = commune::findOrFail($communeId);

            $villageId = $this->findOrCreateByName('village', $data['village_name'], ['commune_id' => $commune->id]);
            village::findOrFail($villageId);
        });

        return redirect()
            ->route('provinces.create')
            ->with('success', 'Address saved. Existing province, district, commune, or village records were reused.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $province = Province::with('districts')->findOrFail($id);

        return view('address.show', compact('province'));
    }

    public function createDistrict(Province $province)
    {
        return view('address.district.create', compact('province'));
    }

    public function storeDistrict(Request $request, Province $province)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $province->districts()->firstOrCreate(['name' => $data['name']]);

        return redirect()
            ->route('provinces.show', $province)
            ->with('success', 'District saved. An existing district with this name was reused if present.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $province = Province::findOrFail($id);

        return view('address.province.edit', compact('province'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $province = Province::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $province->update($data);

        return redirect()
            ->route('provinces.index')
            ->with('success', 'Province updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $province = Province::findOrFail($id);
        $province->delete();

        return redirect()
            ->route('provinces.index')
            ->with('success', 'Province and its districts deleted successfully.');
    }
}
