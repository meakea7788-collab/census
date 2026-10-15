<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GroupSchool;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_name(): void
    {
        $response = $this->post('/users/store', [
            'name' => 'Alice Johnson',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'Alice Johnson',
        ]);
    }

    public function test_developer_account_can_login(): void
    {
        User::create([
            'name' => 'Developer',
            'email' => 'dev@census.local',
            'password' => 'Developer@123',
            'role' => User::ROLE_SUPERADMIN,
        ]);

        $response = $this->post('/login', [
            'email' => 'dev@census.local',
            'password' => 'Developer@123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_failed_login_displays_an_error_and_preserves_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'unknown@census.local',
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');

        $this->get('/login')
            ->assertSee('The provided credentials do not match our records.')
            ->assertSee('value="unknown@census.local"', false);
    }

    public function test_group_school_form_can_be_submitted_and_saved(): void
    {
        $province = Province::create(['name' => 'Central Province']);
        $district = \App\Models\District::create([
            'name' => 'North District',
            'province_id' => $province->id,
        ]);
        $commune = \App\Models\commune::create([
            'name' => 'River Commune',
            'district_id' => $district->id,
        ]);
        $village = \App\Models\village::create([
            'name' => 'Green Village',
            'commune_id' => $commune->id,
        ]);

        $this->get(route('group-school.create'))
            ->assertOk()
            ->assertSee(route('group-school.store'), false)
            ->assertSee('province_id')
            ->assertSee('district_id')
            ->assertSee('commune_id')
            ->assertSee('village_id')
            ->assertSee('Central Province');

        $response = $this->post(route('group-school.store'), [
            'name' => 'Northside School Group',
            'director_name' => 'Jordan Lee',
            'phone' => '555-0100',
            'province_id' => $province->id,
            'district_id' => $district->id,
            'commune_id' => $commune->id,
            'village_id' => $village->id,
        ]);

        $response->assertRedirect(route('group-school.create'));
        $response->assertSessionHas('status', 'Group school created successfully.');
        $this->assertDatabaseHas('group_schools', [
            'name' => 'Northside School Group',
            'director_name' => 'Jordan Lee',
            'phone' => '555-0100',
            'address' => 'Green Village, River Commune, North District, Central Province',
        ]);
        $this->assertSame(1, GroupSchool::count());
    }

    public function test_group_school_rejects_a_location_outside_the_selected_parent(): void
    {
        $province = Province::create(['name' => 'Central Province']);
        $otherProvince = Province::create(['name' => 'Eastern Province']);
        $district = \App\Models\District::create([
            'name' => 'Eastern District',
            'province_id' => $otherProvince->id,
        ]);
        $commune = \App\Models\commune::create([
            'name' => 'River Commune',
            'district_id' => $district->id,
        ]);
        $village = \App\Models\village::create([
            'name' => 'Green Village',
            'commune_id' => $commune->id,
        ]);

        $this->from(route('group-school.create'))
            ->post(route('group-school.store'), [
                'name' => 'Invalid Location Group School',
                'director_name' => 'Jordan Lee',
                'phone' => '555-0100',
                'province_id' => $province->id,
                'district_id' => $district->id,
                'commune_id' => $commune->id,
                'village_id' => $village->id,
            ])
            ->assertRedirect(route('group-school.create'))
            ->assertSessionHasErrors('district_id');

        $this->assertDatabaseMissing('group_schools', [
            'name' => 'Invalid Location Group School',
        ]);
    }

    public function test_group_school_index_displays_saved_schools(): void
    {
        GroupSchool::create([
            'name' => 'Northside School Group',
            'director_name' => 'Jordan Lee',
            'phone' => '555-0100',
            'address' => '12 Main Street',
        ]);

        $this->get(route('group-school.index'))
            ->assertOk()
            ->assertSee('Group Schools')
            ->assertSee('Northside School Group')
            ->assertSee('Jordan Lee')
            ->assertSee('555-0100')
            ->assertSee('12 Main Street')
            ->assertSee(route('group-school.create'), false);
    }

    public function test_location_hierarchy_can_be_created(): void
    {
        $this->get(route('provinces.create'))
            ->assertOk()
            ->assertSee('Province name')
            ->assertSee('District name')
            ->assertSee('Commune name')
            ->assertSee('Village name');

        $response = $this->post(route('provinces.store'), [
            'province_name' => 'Central Province',
            'district_name' => 'North District',
            'commune_name' => 'River Commune',
            'village_name' => 'Green Village',
        ]);

        $response->assertRedirect(route('provinces.create'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('province', ['name' => 'Central Province']);
        $this->assertDatabaseHas('district', [
            'name' => 'North District',
            'province_id' => Province::where('name', 'Central Province')->value('id'),
        ]);
        $this->assertDatabaseHas('commune', ['name' => 'River Commune']);
        $this->assertDatabaseHas('village', ['name' => 'Green Village']);

        $this->post(route('provinces.store'), [
            'province_name' => 'Central Province',
            'district_name' => 'North District',
            'commune_name' => 'River Commune',
            'village_name' => 'Green Village',
        ])->assertRedirect(route('provinces.create'));

        $this->assertSame(1, Province::where('name', 'Central Province')->count());
        $this->assertSame(1, \App\Models\District::where('name', 'North District')->count());
        $this->assertSame(1, \App\Models\commune::where('name', 'River Commune')->count());
        $this->assertSame(1, \App\Models\village::where('name', 'Green Village')->count());

        $this->get(route('provinces.index'))
            ->assertOk()
            ->assertSee('Central Province')
            ->assertDontSee('North District')
            ->assertSee(route('provinces.create'), false)
            ->assertSee(route('provinces.show', Province::where('name', 'Central Province')->value('id')), false)
            ->assertSee(route('provinces.edit', Province::where('name', 'Central Province')->value('id')), false)
            ->assertSee(route('provinces.destroy', Province::where('name', 'Central Province')->value('id')), false);

        $this->get(route('provinces.show', Province::where('name', 'Central Province')->value('id')))
            ->assertOk()
            ->assertSee('Central Province')
            ->assertSee('North District')
            ->assertSee('Add District')
            ->assertSee(route('provinces.districts.create', Province::where('name', 'Central Province')->value('id')), false)
            ->assertDontSee('River Commune')
            ->assertSee(route('districts.communes.show', \App\Models\District::where('name', 'North District')->value('id')), false)
            ->assertSee(route('districts.communes.create', \App\Models\District::where('name', 'North District')->value('id')), false);

        $province = Province::where('name', 'Central Province')->firstOrFail();
        $this->get(route('provinces.districts.create', $province))
            ->assertOk()
            ->assertSee('Add a district to Central Province province.');

        $this->post(route('provinces.districts.store', $province), [
            'name' => 'South District',
        ])->assertRedirect(route('provinces.show', $province));

        $this->post(route('provinces.districts.store', $province), [
            'name' => 'South District',
        ])->assertRedirect(route('provinces.show', $province));

        $this->assertDatabaseHas('district', [
            'name' => 'South District',
            'province_id' => $province->id,
        ]);
        $this->assertSame(1, \App\Models\District::where('name', 'South District')->where('province_id', $province->id)->count());

        $district = \App\Models\District::where('name', 'North District')->firstOrFail();
        $this->get(route('districts.communes.show', $district))
            ->assertOk()
            ->assertSee('River Commune')
            ->assertSee(route('communes.show', \App\Models\commune::where('name', 'River Commune')->value('id')), false)
            ->assertSee(route('communes.edit', \App\Models\commune::where('name', 'River Commune')->value('id')), false)
            ->assertSee(route('communes.destroy', \App\Models\commune::where('name', 'River Commune')->value('id')), false);

        $commune = \App\Models\commune::where('name', 'River Commune')->firstOrFail();
        $this->get(route('communes.show', $commune))
            ->assertOk()
            ->assertSee('River Commune')
            ->assertSee('Add Commune')
            ->assertSee(route('districts.communes.create', $district), false)
            ->assertSee('Add Village')
            ->assertSee(route('communes.villages.create', $commune), false)
            ->assertSee(route('communes.edit', $commune), false)
            ->assertSee(route('communes.destroy', $commune), false)
            ->assertSee('Green Village');

        $this->get(route('communes.villages.create', $commune))
            ->assertOk()
            ->assertSee('Add a village to River Commune commune.');

        $this->post(route('communes.villages.store', $commune), [
            'name' => 'New Village',
        ])->assertRedirect(route('communes.show', $commune));

        $this->assertDatabaseHas('village', [
            'name' => 'New Village',
            'commune_id' => $commune->id,
        ]);

        $this->get(route('communes.edit', $commune))
            ->assertOk()
            ->assertSee('River Commune');

        $this->put(route('communes.update', $commune), [
            'name' => 'Updated River Commune',
        ])->assertRedirect(route('districts.communes.show', $district));

        $this->assertDatabaseHas('commune', [
            'id' => $commune->id,
            'name' => 'Updated River Commune',
        ]);

        $this->get(route('districts.communes.create', $district))
            ->assertOk()
            ->assertSee('Add Commune');

        $this->post(route('districts.communes.store', $district), [
            'name' => 'Hill Commune',
        ])->assertRedirect(route('districts.communes.show', $district));

        $this->post(route('districts.communes.store', $district), [
            'name' => 'Hill Commune',
        ])->assertRedirect(route('districts.communes.show', $district));

        $this->assertDatabaseHas('commune', [
            'name' => 'Hill Commune',
            'district_id' => $district->id,
        ]);
        $this->assertSame(1, \App\Models\commune::where('name', 'Hill Commune')->where('district_id', $district->id)->count());

        $this->post(route('communes.villages.store', $commune), [
            'name' => 'New Village',
        ])->assertRedirect(route('communes.show', $commune));
        $this->assertSame(1, \App\Models\village::where('name', 'New Village')->where('commune_id', $commune->id)->count());

        $this->delete(route('communes.destroy', $commune))
            ->assertRedirect(route('districts.communes.show', $district));

        $this->assertDatabaseMissing('commune', ['id' => $commune->id]);
        $this->assertDatabaseMissing('village', ['name' => 'Green Village']);
    }

    public function test_province_can_be_edited_and_deleted(): void
    {
        $province = Province::create(['name' => 'Old Province']);

        $this->get(route('provinces.edit', $province->id))
            ->assertOk()
            ->assertSee('Old Province');

        $this->put(route('provinces.update', $province->id), [
            'name' => 'Updated Province',
        ])->assertRedirect(route('provinces.index'));

        $this->assertDatabaseHas('province', ['id' => $province->id, 'name' => 'Updated Province']);

        $this->delete(route('provinces.destroy', $province->id))
            ->assertRedirect(route('provinces.index'));

        $this->assertDatabaseMissing('province', ['id' => $province->id]);
    }
}
