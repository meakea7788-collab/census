<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HoleHouseSeeder extends Seeder
{
    public function run(): void
    {
        $yearId = DB::table('year')->where('year', 2026)->value('id');
        $provinceId = DB::table('province')->where('name', 'សៀមរាប')->value('id');
        $districtId = DB::table('district')
            ->where('name', 'ពួក')
            ->where('province_id', $provinceId)
            ->value('id');
        $communeId = DB::table('commune')
            ->where('name', 'ត្រីញ័រ')
            ->where('district_id', $districtId)
            ->value('id');
        $villageId = DB::table('village')
            ->where('name', 'ធិបតី')
            ->where('commune_id', $communeId)
            ->value('id');
        $school = DB::table('schools')
            ->where('name', 'បឋមសិក្សាអធិបតី')
            ->first(['id', 'group_school_id']);

        if (! $yearId || ! $provinceId || ! $districtId || ! $communeId || ! $villageId || ! $school) {
            throw new RuntimeException('Unable to seed hole houses because the required 2026 Siem Reap demo data is missing.');
        }

        $censusId = DB::table('census')
            ->where('year_id', $yearId)
            ->where('village_id', $villageId)
            ->value('id');

        if (! $censusId) {
            $censusId = DB::table('census')->insertGetId([
                'year_id' => $yearId,
                'group_school_id' => $school->group_school_id,
                'school_id' => $school->id,
                'province_id' => $provinceId,
                'district_id' => $districtId,
                'commune_id' => $communeId,
                'village_id' => $villageId,
                'note' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $households = [
            [
                'hole_number' => 1,
                'leader_family_name' => 'ម៉ កា',
                'phone_number' => '0968689680',
            ],
            [
                'hole_number' => 2,
                'leader_family_name' => 'ទ្រង់ លេបយ',
                'phone_number' => '09484744',
            ],
        ];

        foreach ($households as $household) {
            $identity = [
                'census_id' => $censusId,
                'hole_number' => $household['hole_number'],
            ];
            $values = [
                'leader_family_name' => $household['leader_family_name'],
                'phone_number' => $household['phone_number'],
                'address' => null,
                'village_id' => $villageId,
                'status' => 'Occupied',
                'updated_at' => now(),
            ];
            $existingHouseId = DB::table('holehouse')->where($identity)->value('id');

            if ($existingHouseId) {
                DB::table('holehouse')->where('id', $existingHouseId)->update($values);
            } else {
                DB::table('holehouse')->insert($identity + $values + ['created_at' => now()]);
            }
        }
    }
}
