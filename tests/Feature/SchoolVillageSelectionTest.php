<?php

namespace Tests\Feature;

use App\Models\GroupSchool;
use App\Models\Province;
use App\Models\School;
use App\Models\commune;
use App\Models\village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolVillageSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_can_be_assigned_multiple_villages_and_updated(): void
    {
        [$province, $district, $commune, $villages, $groupSchool] = $this->createSchoolLocations();

        $this->get(route('schools.create'))
            ->assertOk()
            ->assertSee('input.type = \'checkbox\'', false)
            ->assertSee('input.name = \'village_ids[]\'', false)
            ->assertSee('village_options', false);
        $this->get(route('group-school.show', $groupSchool))
            ->assertOk()
            ->assertSee(route('schools.create', ['group_school_id' => $groupSchool->id]), false);
        $this->get(route('schools.create', ['group_school_id' => $groupSchool->id]))
            ->assertOk()
            ->assertSee('province: '.$province->id, false)
            ->assertSee('district: '.$district->id, false)
            ->assertSee('commune: '.$commune->id, false)
            ->assertSee('groupSchool: '.$groupSchool->id, false);

        $this->post(route('schools.store'), [
            'name' => 'River School',
            'province_id' => $province->id,
            'district_id' => $district->id,
            'commune_id' => $commune->id,
            'village_ids' => $villages->pluck('id')->all(),
            'group_school_id' => $groupSchool->id,
        ])->assertRedirect(route('schools.index'));

        $school = School::where('name', 'River School')->firstOrFail();
        $this->assertSame(
            $villages->pluck('id')->sort()->values()->all(),
            $school->villages()->pluck('village.id')->sort()->values()->all()
        );
        $this->get(route('schools.index'))
            ->assertOk()
            ->assertSee($villages->first()->name)
            ->assertSee($villages->last()->name);

        $this->put(route('schools.update', $school), [
            'name' => $school->name,
            'province_id' => $province->id,
            'district_id' => $district->id,
            'commune_id' => $commune->id,
            'village_ids' => [$villages->last()->id],
            'group_school_id' => $groupSchool->id,
        ])->assertRedirect(route('schools.index'));

        $this->assertSame(
            [$villages->last()->id],
            $school->fresh()->villages()->pluck('village.id')->all()
        );

        $this->get(route('schools.show', $school))
            ->assertOk()
            ->assertSee($villages->last()->name);
    }

    public function test_school_rejects_villages_outside_selected_commune(): void
    {
        [$province, $district, $commune, , $groupSchool] = $this->createSchoolLocations();
        $otherCommune = commune::create([
            'name' => 'Other Commune',
            'district_id' => $district->id,
        ]);
        $foreignVillage = village::create([
            'name' => 'Foreign Village',
            'commune_id' => $otherCommune->id,
        ]);

        $this->from(route('schools.create'))
            ->post(route('schools.store'), [
                'name' => 'Invalid School',
                'province_id' => $province->id,
                'district_id' => $district->id,
                'commune_id' => $commune->id,
                'village_ids' => [$foreignVillage->id],
                'group_school_id' => $groupSchool->id,
            ])
            ->assertRedirect(route('schools.create'))
            ->assertSessionHasErrors('village_ids.0');

        $this->assertDatabaseMissing('schools', ['name' => 'Invalid School']);
    }

    private function createSchoolLocations(): array
    {
        $province = Province::create(['name' => 'Central Province']);
        $district = \App\Models\District::create([
            'name' => 'North District',
            'province_id' => $province->id,
        ]);
        $commune = commune::create([
            'name' => 'River Commune',
            'district_id' => $district->id,
        ]);
        $villages = collect(['Green Village', 'Blue Village'])->map(
            fn (string $name) => village::create([
                'name' => $name,
                'commune_id' => $commune->id,
            ])
        );
        $groupSchool = GroupSchool::create([
            'name' => 'River School Group',
            'director_name' => 'Jordan Lee',
            'phone' => '555-0100',
            'address' => 'Green Village, River Commune, North District, Central Province',
        ]);

        return [$province, $district, $commune, $villages, $groupSchool];
    }
}
