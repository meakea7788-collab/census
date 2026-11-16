<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        $censusId = DB::table('census')
            ->join('year', 'census.year_id', '=', 'year.id')
            ->join('village', 'census.village_id', '=', 'village.id')
            ->where('year.year', 2026)
            ->where('village.name', 'ធិបតី')
            ->value('census.id');

        if (! $censusId) {
            throw new RuntimeException('Unable to seed family members because the 2026 Thipatey census is missing.');
        }

        $houseIds = DB::table('holehouse')
            ->where('census_id', $censusId)
            ->whereIn('hole_number', [1, 2])
            ->pluck('id', 'hole_number');

        if (! $houseIds->has(1) || ! $houseIds->has(2)) {
            throw new RuntimeException('Unable to seed family members because demo houses 1 and 2 are missing.');
        }

        $members = [
            [
                'hole_number' => 1,
                'family_name' => 'កាយ ចនា',
                'gender' => 'ប្រុស',
                'date_of_birth' => '2005-01-09',
                'relationship' => 'កូន',
                'job' => 'កសិករ',
            ],
            [
                'hole_number' => 1,
                'family_name' => 'សុភី',
                'gender' => 'ស្រី',
                'date_of_birth' => '2010-01-22',
                'relationship' => 'កូន',
                'job' => 'សិស្ស',
            ],
            [
                'hole_number' => 2,
                'family_name' => 'ក',
                'gender' => 'ស្រី',
                'date_of_birth' => '2009-01-23',
                'relationship' => 'កូន',
                'job' => 'សិស្ស',
            ],
            [
                'hole_number' => 2,
                'family_name' => 'chandy',
                'gender' => 'ប្រុស',
                'date_of_birth' => '2009-01-03',
                'relationship' => 'កូន',
                'job' => 'បុគ្គលិក',
            ],
        ];

        foreach ($members as $member) {
            $identity = [
                'holehouse_id' => $houseIds[$member['hole_number']],
                'family_name' => $member['family_name'],
                'date_of_birth' => $member['date_of_birth'],
            ];
            $values = [
                'gender' => $member['gender'],
                'relationship' => $member['relationship'],
                'job' => $member['job'],
                'updated_at' => now(),
            ];
            $existingMemberId = DB::table('family')->where($identity)->value('id');

            if ($existingMemberId) {
                DB::table('family')->where('id', $existingMemberId)->update($values);
            } else {
                DB::table('family')->insert($identity + $values + ['created_at' => now()]);
            }
        }
    }
}
