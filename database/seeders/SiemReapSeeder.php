<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiemReapSeeder extends Seeder
{
    public function run(): void
    {
        $provinceId = DB::table('province')
            ->where('name', 'សៀមរាប')
            ->value('id');

        if (! $provinceId) {
            $provinceId = DB::table('province')->insertGetId([
                'name' => 'សៀមរាប',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $dataset = json_decode(
            file_get_contents(__DIR__.'/data/siem_reap.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $districtIds = [];

        foreach ($dataset['districts'] as $district) {
            $districtId = DB::table('district')
                ->where('name', $district['nameKm'])
                ->where('province_id', $provinceId)
                ->value('id');

            if (! $districtId) {
                $districtId = DB::table('district')->insertGetId([
                    'name' => $district['nameKm'],
                    'province_id' => $provinceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $districtIds[$district['code']] = $districtId;
        }

        $communeIds = [];
        foreach ($dataset['communes'] as $commune) {
            $districtId = $districtIds[$commune['districtCode']] ?? null;

            if (! $districtId) {
                throw new \RuntimeException(
                    "Missing Siem Reap district for commune {$commune['code']}."
                );
            }

            $communeId = DB::table('commune')
                ->where('name', $commune['nameKm'])
                ->where('district_id', $districtId)
                ->value('id');

            if (! $communeId) {
                $communeId = DB::table('commune')->insertGetId([
                    'name' => $commune['nameKm'],
                    'district_id' => $districtId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $communeIds[$commune['code']] = $communeId;
        }

        foreach ($dataset['villages'] as $village) {
            $communeId = $communeIds[$village['communeCode']] ?? null;

            if (! $communeId) {
                throw new \RuntimeException(
                    "Missing Siem Reap commune for village {$village['nameKm']}."
                );
            }

            $exists = DB::table('village')
                ->where('name', $village['nameKm'])
                ->where('commune_id', $communeId)
                ->exists();

            if (! $exists) {
                DB::table('village')->insert([
                    'name' => $village['nameKm'],
                    'commune_id' => $communeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
