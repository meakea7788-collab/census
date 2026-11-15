<?php

namespace Tests\Feature;

use App\Models\GroupSchool;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_and_deactivate_a_user_in_their_school(): void
    {
        $adminEmail = 'admin-' . uniqid() . '@example.test';
        $userEmail = 'member-' . uniqid() . '@example.test';
        $school = School::create([
            'name' => 'North School',
            'group_school_id' => GroupSchool::create([
                'name' => 'North Group',
                'director_name' => 'Director',
                'phone' => '555-0100',
                'address' => 'Test address',
            ])->id,
        ]);
        $teacher = $school->teachers()->create([
            'name' => 'North Teacher',
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0101',
        ]);
        $this->assignTeacherToCensus($teacher);
        $admin = User::create([
            'name' => 'School Admin',
            'email' => $adminEmail,
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $teacher->id,
        ]);
        $user = User::create([
            'name' => 'Old Name',
            'email' => $userEmail,
            'password' => 'password',
            'role' => User::ROLE_USER,
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Active')
            ->assertSee(route('users.edit', $user), false)
            ->assertSee(route('users.active', $user), false);

        $this->get(route('users.edit', $user))
            ->assertOk()
            ->assertSee('Old Name')
            ->assertSee('North Group');

        $this->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $userEmail,
            'teacher_id' => $teacher->id,
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => User::ROLE_USER,
        ]);

        $this->patch(route('users.active', $user))->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);

        auth()->logout();
        $this->post(route('login.submit'), [
            'email' => $userEmail,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_cannot_edit_or_toggle_another_schools_user(): void
    {
        $adminEmail = 'north-admin-' . uniqid() . '@example.test';
        $otherUserEmail = 'south-user-' . uniqid() . '@example.test';
        $otherAdminEmail = 'another-north-admin-' . uniqid() . '@example.test';
        $ownTeacher = $this->createTeacher('North');
        $otherTeacher = $this->createTeacher('South');
        $admin = User::create([
            'name' => 'North Admin',
            'email' => $adminEmail,
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $ownTeacher->id,
        ]);
        $otherUser = User::create([
            'name' => 'South User',
            'email' => $otherUserEmail,
            'password' => 'password',
            'role' => User::ROLE_USER,
            'teacher_id' => $otherTeacher->id,
        ]);
        $sameSchoolAdmin = User::create([
            'name' => 'Another North Admin',
            'email' => $otherAdminEmail,
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $ownTeacher->id,
        ]);

        $this->actingAs($admin)
            ->get(route('users.edit', $otherUser))
            ->assertNotFound();
        $this->patch(route('users.active', $otherUser))
            ->assertNotFound();
        $this->assertDatabaseHas('users', ['id' => $otherUser->id, 'is_active' => true]);
        $this->get(route('users.edit', $sameSchoolAdmin))->assertForbidden();
        $this->patch(route('users.active', $sameSchoolAdmin))->assertForbidden();
    }

    public function test_admin_cannot_assign_a_teacher_not_selected_in_their_school_census(): void
    {
        $teacher = $this->createTeacher('North');
        $admin = User::create([
            'name' => 'North Admin',
            'email' => 'north-admin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'New Member',
                'teacher_id' => $teacher->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['name' => 'New Member']);
    }

    public function test_admin_can_assign_multiple_census_teachers_to_a_user(): void
    {
        $firstTeacher = $this->createTeacher('North');
        $secondTeacher = $firstTeacher->school->teachers()->create([
            'name' => 'North Secondary Teacher',
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0110',
        ]);
        $this->assignTeacherToCensus($firstTeacher);
        $this->assignTeacherToCensus($secondTeacher);
        $admin = User::create([
            'name' => 'North Admin',
            'email' => 'north-admin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'teacher_id' => $firstTeacher->id,
        ]);

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertSee('name="teacher_ids[]"', false)
            ->assertSee('North Group')
            ->assertSee('North Teacher');

        $this->post(route('users.store'), [
            'name' => 'Multi Teacher User',
            'teacher_ids' => [$firstTeacher->id, $secondTeacher->id],
        ])->assertRedirect(route('users.create'));

        $user = User::where('name', 'Multi Teacher User')->firstOrFail();
        $this->assertSame(
            [$firstTeacher->id, $secondTeacher->id],
            $user->teachers()->orderBy('teacher.id')->pluck('teacher.id')->all()
        );
        $this->assertSame($firstTeacher->id, $user->teacher_id);

        $this->get(route('users.edit', $user))
            ->assertOk()
            ->assertSee('value="' . $firstTeacher->id . '"', false)
            ->assertSee('value="' . $secondTeacher->id . '"', false);

        $this->put(route('users.update', $user), [
            'name' => 'Multi Teacher User',
            'email' => $user->email,
            'teacher_ids' => [$secondTeacher->id],
        ])->assertRedirect(route('users.index'));
        $this->assertSame([$secondTeacher->id], $user->fresh()->teachers()->pluck('teacher.id')->all());
    }

    public function test_superadmin_selects_a_school_without_a_teacher_list_when_creating_user(): void
    {
        $school = $this->createTeacher('North')->school;
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => User::ROLE_SUPERADMIN,
        ]);

        $this->actingAs($superadmin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertSee('name="school_id"', false)
            ->assertSee($school->name)
            ->assertDontSee('name="teacher_ids[]"', false)
            ->assertDontSee('No teachers assigned to a census');

        $this->post(route('users.store'), [
            'name' => 'Assigned Admin',
            'role' => User::ROLE_ADMIN,
            'school_id' => $school->id,
        ])->assertRedirect(route('users.create'));

        $createdUser = User::where('name', 'Assigned Admin')->firstOrFail();
        $this->assertSame($school->id, $createdUser->school_id);
        $this->assertSame(User::ROLE_ADMIN, $createdUser->role);
        $this->assertNull($createdUser->teacher_id);
        $this->assertSame([], $createdUser->teachers()->pluck('teacher.id')->all());
    }

    private function createTeacher(string $schoolName)
    {
        $school = School::create([
            'name' => $schoolName . ' School',
            'group_school_id' => GroupSchool::create([
                'name' => $schoolName . ' Group',
                'director_name' => 'Director',
                'phone' => '555-0100',
                'address' => 'Test address',
            ])->id,
        ]);

        return $school->teachers()->create([
            'name' => $schoolName . ' Teacher',
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0101',
        ]);
    }

    private function assignTeacherToCensus(\App\Models\Teacher $teacher): void
    {
        $year = \App\Models\Year::create([
            'year' => random_int(2000, 2100),
            'description' => 'User management test',
            'is_active' => true,
        ]);
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
        $censusId = DB::table('census')->insertGetId([
            'year_id' => $year->id,
            'group_school_id' => $teacher->school->group_school_id,
            'school_id' => $teacher->school_id,
            'province_id' => $provinceId,
            'district_id' => $districtId,
            'commune_id' => $communeId,
            'village_id' => $villageId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('census_teacher')->insert([
            'census_id' => $censusId,
            'teacher_id' => $teacher->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
