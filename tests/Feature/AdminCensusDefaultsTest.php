<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\GroupSchool;
use App\Models\Province;
use App\Models\School;
use App\Models\User;
use App\Models\Year;
use App\Models\commune;
use App\Models\village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCensusDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_census_form_defaults_school_group_and_address_and_uses_them_on_save(): void
    {
        $province = Province::create(['name' => 'Central Province']);
        $district = District::create(['name' => 'North District', 'province_id' => $province->id]);
        $commune = commune::create(['name' => 'River Commune', 'district_id' => $district->id]);
        $village = village::create(['name' => 'Green Village', 'commune_id' => $commune->id]);
        $groupSchool = GroupSchool::create([
            'name' => 'River School Group',
            'director_name' => 'Director',
            'phone' => '555-0100',
            'address' => 'Green Village, River Commune, North District, Central Province',
        ]);
        $school = School::create([
            'name' => 'River School',
            'group_school_id' => $groupSchool->id,
            'address' => 'Green Village, River Commune, North District, Central Province',
        ]);
        $school->villages()->attach($village->id);
        $teacher = $school->teachers()->create([
            'name' => 'River Teacher',
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0101',
        ]);
        $admin = User::create([
            'name' => 'River Admin',
            'email' => 'river-admin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $teacher->id,
        ]);
        $year = Year::create([
            'year' => random_int(2000, 2100),
            'description' => 'Admin census test',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('census.create'))
            ->assertOk()
            ->assertSee('name="group_school_id" value="' . $groupSchool->id . '"', false)
            ->assertSee('name="school_id" value="' . $school->id . '"', false)
            ->assertSee('province: ' . $province->id, false)
            ->assertSee('district: ' . $district->id, false)
            ->assertSee('commune: ' . $commune->id, false)
            ->assertSee('village: ' . $village->id, false);

        $this->post(route('census.store'), [
            'year_id' => $year->id,
            'group_school_id' => $groupSchool->id,
            'school_id' => $school->id,
            'province_id' => $province->id,
            'district_id' => $district->id,
            'commune_id' => $commune->id,
            'village_id' => $village->id,
            'teacher_id' => [$teacher->id],
        ])->assertRedirect(route('census.index'));

        $this->assertDatabaseHas('census', [
            'school_id' => $school->id,
            'group_school_id' => $groupSchool->id,
            'province_id' => $province->id,
            'district_id' => $district->id,
            'commune_id' => $commune->id,
            'village_id' => $village->id,
        ]);
        $this->assertDatabaseHas('census_teacher', [
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_admin_cannot_create_census_for_a_village_not_assigned_to_their_school(): void
    {
        $province = Province::create(['name' => 'Central Province']);
        $district = District::create(['name' => 'North District', 'province_id' => $province->id]);
        $commune = commune::create(['name' => 'River Commune', 'district_id' => $district->id]);
        $assignedVillage = village::create(['name' => 'Assigned Village', 'commune_id' => $commune->id]);
        $otherVillage = village::create(['name' => 'Other Village', 'commune_id' => $commune->id]);
        $groupSchool = GroupSchool::create([
            'name' => 'River School Group',
            'director_name' => 'Director',
            'phone' => '555-0100',
            'address' => 'River Commune, North District',
        ]);
        $school = School::create(['name' => 'River School', 'group_school_id' => $groupSchool->id]);
        $school->villages()->attach($assignedVillage->id);
        $teacher = $school->teachers()->create([
            'name' => 'River Teacher',
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0101',
        ]);
        $admin = User::create([
            'name' => 'River Admin',
            'email' => 'river-admin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $teacher->id,
        ]);
        $year = Year::create([
            'year' => random_int(2000, 2100),
            'description' => 'Admin census test',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('census.store'), [
                'year_id' => $year->id,
                'group_school_id' => $groupSchool->id,
                'school_id' => $school->id,
                'province_id' => $province->id,
                'district_id' => $district->id,
                'commune_id' => $commune->id,
                'village_id' => $otherVillage->id,
                'teacher_id' => [$teacher->id],
            ])
            ->assertSessionHasErrors('village_id');

        $this->assertDatabaseMissing('census', ['village_id' => $otherVillage->id]);
    }
}
