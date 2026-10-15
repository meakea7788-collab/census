<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GroupSchool;
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
        $this->get(route('group-school.create'))
            ->assertOk()
            ->assertSee(route('group-school.store'), false);

        $response = $this->post(route('group-school.store'), [
            'name' => 'Northside School Group',
            'director_name' => 'Jordan Lee',
            'phone' => '555-0100',
            'address' => '12 Main Street',
        ]);

        $response->assertRedirect(route('group-school.create'));
        $response->assertSessionHas('status', 'Group school created successfully.');
        $this->assertDatabaseHas('group_schools', [
            'name' => 'Northside School Group',
            'director_name' => 'Jordan Lee',
            'phone' => '555-0100',
            'address' => '12 Main Street',
        ]);
        $this->assertSame(1, GroupSchool::count());
    }
}
