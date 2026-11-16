<?php

namespace Tests\Feature;

use App\Models\GroupSchool;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Year;
use App\Models\hole_house;
use App\Models\family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SchoolIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_lists_only_the_school_linked_to_their_teacher(): void
    {
        $ownSchool = $this->createSchool('North School', 'North Teacher');
        $otherSchool = $this->createSchool('South School', 'South Teacher');
        $ownSchool->teachers()->create([
            'name' => 'North Unselected Teacher',
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0110',
        ]);
        $ownCensusId = $this->createCensusForSchool($ownSchool);
        $otherCensusId = $this->createCensusForSchool($otherSchool);
        DB::table('census_teacher')->insert([
            [
                'census_id' => $ownCensusId,
                'teacher_id' => $ownSchool->teachers()->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'census_id' => $otherCensusId,
                'teacher_id' => $otherSchool->teachers()->first()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        User::create([
            'name' => 'South User',
            'email' => 'south-isolation-user-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_USER,
            'teacher_id' => $otherSchool->teachers()->first()->id,
        ]);

        $this->actingAs(User::create([
            'name' => 'North Admin',
            'email' => 'north-admin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $ownSchool->teachers()->first()->id,
        ]));

        $this->get(route('schools.index'))
            ->assertOk()
            ->assertSee('North School')
            ->assertDontSee('South School');

        $this->get(route('teachers.index'))
            ->assertOk()
            ->assertSee('North Teacher')
            ->assertDontSee('South Teacher');
        $this->get(route('group-school.index'))
            ->assertOk()
            ->assertSee('North School Group')
            ->assertDontSee('South School Group');
        $this->get(route('group-school.show', $ownSchool->group_school_id))
            ->assertOk()
            ->assertSee('North School')
            ->assertDontSee('South School');
        $this->get(route('users.index'))
            ->assertOk()
            ->assertSee('North Admin')
            ->assertDontSee('South User');
        $this->get(route('census.create'))
            ->assertOk()
            ->assertSee('North Teacher')
            ->assertDontSee('South Teacher');
        $this->get(route('users.create'))
            ->assertOk()
            ->assertSee('North Teacher')
            ->assertSee('North School Group')
            ->assertSee('name="teacher_ids[]"', false)
            ->assertDontSee('North Unselected Teacher')
            ->assertDontSee('South Teacher');
    }

    public function test_admin_without_a_school_link_cannot_load_school_data(): void
    {
        $this->actingAs(User::create([
            'name' => 'Unlinked Admin',
            'email' => 'unlinked-admin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]));

        $this->get(route('schools.index'))->assertForbidden();
    }

    public function test_admin_lists_and_opens_only_household_data_from_their_school(): void
    {
        $ownSchool = $this->createSchool('North School', 'North Teacher');
        $otherSchool = $this->createSchool('South School', 'South Teacher');
        $year = $this->createYear();
        $provinceId = DB::table('province')->insertGetId(['name' => 'Test Province', 'created_at' => now(), 'updated_at' => now()]);
        $districtId = DB::table('district')->insertGetId([
            'name' => 'Test District',
            'province_id' => $provinceId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $communeId = DB::table('commune')->insertGetId([
            'name' => 'Test Commune',
            'district_id' => $districtId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $villageId = DB::table('village')->insertGetId([
            'name' => 'Test Village',
            'commune_id' => $communeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ownCensusId = $this->createCensus($ownSchool, $year, $provinceId, $districtId, $communeId, $villageId);
        $otherCensusId = $this->createCensus($otherSchool, $year, $provinceId, $districtId, $communeId, $villageId);
        $ownHouse = $this->createHouse($ownCensusId, $villageId, 'North Household');
        $otherHouse = $this->createHouse($otherCensusId, $villageId, 'South Household');
        family::create([
            'holehouse_id' => $ownHouse->id,
            'family_name' => 'North Family',
            'gender' => 'Female',
            'date_of_birth' => '2000-01-01',
            'relationship' => 'Head',
            'job' => 'Teacher',
        ]);
        $otherFamily = family::create([
            'holehouse_id' => $otherHouse->id,
            'family_name' => 'South Family',
            'gender' => 'Female',
            'date_of_birth' => '2000-01-01',
            'relationship' => 'Head',
            'job' => 'Teacher',
        ]);

        $this->actingAs(User::create([
            'name' => 'North Admin',
            'email' => 'north-data-admin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $ownSchool->teachers()->first()->id,
        ]));

        $this->get(route('census.index'))
            ->assertOk()
            ->assertSee('North School')
            ->assertDontSee('South School');
        $this->get(route('hole_houses.index'))
            ->assertOk()
            ->assertSee('North Household')
            ->assertDontSee('South Household');
        $this->get(route('family.index'))
            ->assertOk()
            ->assertSee('North Family')
            ->assertDontSee('South Family');
        $this->get(route('census.show', $otherCensusId))->assertRedirect(route('census.index'));
        $this->get(route('hole_houses.show', $otherHouse->id))->assertNotFound();
        $this->get(route('family.show', $otherFamily->id))->assertNotFound();
    }

    private function createSchool(string $schoolName, string $teacherName): School
    {
        $groupSchool = GroupSchool::create([
            'name' => $schoolName . ' Group',
            'director_name' => 'Director',
            'phone' => '555-0100',
            'address' => 'Test address',
        ]);
        $school = School::create([
            'name' => $schoolName,
            'group_school_id' => $groupSchool->id,
        ]);

        $school->teachers()->create([
            'name' => $teacherName,
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0101',
        ]);

        return $school;
    }

    private function createCensus(School $school, Year $year, int $provinceId, int $districtId, int $communeId, int $villageId): int
    {
        return DB::table('census')->insertGetId([
            'year_id' => $year->id,
            'group_school_id' => $school->group_school_id,
            'school_id' => $school->id,
            'province_id' => $provinceId,
            'district_id' => $districtId,
            'commune_id' => $communeId,
            'village_id' => $villageId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createCensusForSchool(School $school): int
    {
        $year = $this->createYear();
        $provinceId = DB::table('province')->insertGetId([
            'name' => 'Test Province',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $districtId = DB::table('district')->insertGetId([
            'name' => 'Test District',
            'province_id' => $provinceId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $communeId = DB::table('commune')->insertGetId([
            'name' => 'Test Commune',
            'district_id' => $districtId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $villageId = DB::table('village')->insertGetId([
            'name' => 'Test Village',
            'commune_id' => $communeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->createCensus($school, $year, $provinceId, $districtId, $communeId, $villageId);
    }

    private function createYear(): Year
    {
        do {
            $yearNumber = random_int(2000, 2100);
        } while (Year::where('year', $yearNumber)->exists());

        return Year::create([
            'year' => $yearNumber,
            'description' => 'Test year',
            'is_active' => true,
        ]);
    }

    private function createHouse(int $censusId, int $villageId, string $name): hole_house
    {
        return hole_house::create([
            'census_id' => $censusId,
            'hole_number' => 1,
            'leader_family_name' => $name,
            'phone_number' => '555-0102',
            'village_id' => $villageId,
            'status' => 'Active',
        ]);
    }
}
