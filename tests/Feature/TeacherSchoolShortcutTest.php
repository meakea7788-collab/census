<?php

namespace Tests\Feature;

use App\Models\GroupSchool;
use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherSchoolShortcutTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_details_can_open_teacher_form_with_school_selected(): void
    {
        $groupSchool = GroupSchool::create([
            'name' => 'Central School Group',
            'director_name' => 'Jordan Lee',
            'phone' => '555-0100',
            'address' => 'Central Province',
        ]);
        $school = School::create([
            'name' => 'River School',
            'group_school_id' => $groupSchool->id,
        ]);

        $this->get(route('schools.show', $school))
            ->assertOk()
            ->assertSee(route('teachers.create', ['school_id' => $school->id]), false)
            ->assertSee('បន្ថែមគ្រូបង្រៀន');

        $this->get(route('teachers.create', ['school_id' => $school->id]))
            ->assertOk()
            ->assertSee('value="'.$school->id.'" selected', false);

        $this->post(route('teachers.store'), [
            'name' => 'Taylor Smith',
            'sex' => 'Female',
            'position' => 'Teacher',
            'phone' => '555-0101',
            'school_id' => $school->id,
        ])
            ->assertRedirect(route('schools.show', $school))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('teacher', [
            'name' => 'Taylor Smith',
            'school_id' => $school->id,
        ]);
    }
}
