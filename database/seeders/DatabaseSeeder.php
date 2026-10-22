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
          DB::table('address')->insert([
            'province' => 'សៀមរាប',
            'district' => 'ពួក',
            'commune'  => 'ត្រីញ័រ',
            'village'  => 'អធិបតី',
        ]);
    }
}
