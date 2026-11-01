<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'meakkea7777@gmail.com'],
            [
                'name' => 'meakea',
                'password' => '77778888',
                'role' => \App\Models\User::ROLE_SUPERADMIN,
            ]
        );
        $provinceId = DB::table('province')->where('name', 'សៀមរាប')->value('id');

        if (! $provinceId) {
            $provinceId = DB::table('province')->insertGetId([
                'name' => 'សៀមរាប',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $districtId = DB::table('district')
            ->where('name', 'ពួក')
            ->where('province_id', $provinceId)
            ->value('id');

        if (! $districtId) {
            $districtId = DB::table('district')->insertGetId([
                'name' => 'ពួក',
                'province_id' => $provinceId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $communeId = DB::table('commune')
            ->where('name', 'ត្រីញ័រ')
            ->where('district_id', $districtId)
            ->value('id');

        if (! $communeId) {
            $communeId = DB::table('commune')->insertGetId([
                'name' => 'ត្រីញ័រ',
                'district_id' => $districtId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $villages = [
            'ធិបតី',
            'ឈូក',
            'តាហុក',
            'គោកដូង',
            'ស្វាយ',
            'ចំបក់ស',
            'ត្រីញ័រ',
            'ត្រពាំងព្រីង',
            'ធ្វាស',
            'ជួយចក្រី',
        ];

        foreach ($villages as $village) {
            $villageExists = DB::table('village')
                ->where('name', $village)
                ->where('commune_id', $communeId)
                ->exists();

            if (! $villageExists) {
                DB::table('village')->insert([
                    'name' => $village,
                    'commune_id' => $communeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $groupSchoolId = 4;

        DB::table('group_schools')->updateOrInsert(
            ['id' => $groupSchoolId],
            [
                'name' => 'កម្រងអធិបតី',
                'director_name' => 'ឡែម ឌុល',
                'phone' => '0968689680',
                'address' => 'ធិបតី, ត្រីញ័រ, ពួក, សៀមរាប',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $schools = [
            'បឋមសិក្សាអធិបតី',
            'បឋមសិក្សា ថ្មគោល (ឧប)',
            'បឋមសិក្សាស្វាយ',
            'បឋមសិក្សាគោកដូង',
            'បឋមសិក្សាចំបកស',
            'បឋមសិក្សាជួយចក្រី',
        ];

        foreach ($schools as $school) {
            $schoolExists = DB::table('schools')
                ->where('name', $school)
                ->where('group_school_id', $groupSchoolId)
                ->exists();

            if (! $schoolExists) {
                DB::table('schools')->insert([
                    'name' => $school,
                    'group_school_id' => $groupSchoolId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $defaultSchoolId = DB::table('schools')
            ->where('name', 'បឋមសិក្សាអធិបតី')
            ->value('id');
        $thamKhoulSchoolId = DB::table('schools')
            ->where('name', 'បឋមសិក្សា ថ្មគោល (ឧប)')
            ->value('id');

        $teachers = [
            ['កុយ សាវុត', 'Female', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['កិន គី', 'Female', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['រ៉ឹត ហេងលាប', 'FeMale', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ចាប សាវឿន', 'Female', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['សាំ សែន', 'Male', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ធឿន ស៊ីនួន', 'Female', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['គី សម្បត្ដិ', 'Female', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ផាត់ គង់', 'Male', 'នាយករង', '0968689680', $defaultSchoolId],
            ['សន វៃ', 'Male', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ព្រីង សារ័ត្ន', 'Male', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ញ៉យ ប៉ូច', 'Male', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['យូ ផល្លី', 'male', 'នាយក', '0968689680', $defaultSchoolId],
            ['ស៊ុច ភីលា', 'Male', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ឡាយ ស្រីនាង', 'Female', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ញូង ស៊ាន', 'Male', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
            ['ហ៊ាំង ណាវី', 'FeMale', 'គ្រូបង្រៀន', '096868968777', $thamKhoulSchoolId],
            ['ពុំ មករា', 'Female', 'គ្រូបង្រៀន', '096868968777', $thamKhoulSchoolId],
            ['នៀវ សីហា', 'Female', 'គ្រូបង្រៀន', '0968689680', $defaultSchoolId],
        ];

        foreach ($teachers as [$name, $sex, $position, $phone, $schoolId]) {
            if (! $schoolId) {
                continue;
            }

            $exists = DB::table('teacher')
                ->where('name', $name)
                ->where('school_id', $schoolId)
                ->exists();

            if (! $exists) {
                DB::table('teacher')->insert([
                    'name' => $name,
                    'sex' => $sex,
                    'position' => $position,
                    'phone' => $phone,
                    'school_id' => $schoolId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
