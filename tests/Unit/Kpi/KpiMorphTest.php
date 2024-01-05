<?php

namespace Tests\Unit\Kpi;

use App\Models\Kpi\Kpi;
use App\Position;
use App\ProfileGroup;
use App\User;
use Tests\TenantTestCase;

class KpiMorphTest extends TenantTestCase
{
    public function test_it_can_be_morphed()
    {
        // fake data
        $user = User::factory()->create();
        $group = ProfileGroup::factory()->create();
        $position = Position::factory()->create();
        $kpi = Kpi::factory()->create();
        //swap
        $kpi->users()->attach($user);
        $kpi->groups()->attach($group);
        $kpi->positions()->attach($position);
        // asserts
        $this->assertSame($user->kpisMany()->first()->getkey(), $kpi->getkey());
        $this->assertSame($position->kpisMany()->first()->getkey(), $kpi->getkey());
        $this->assertSame($group->kpisMany()->first()->getkey(), $kpi->getkey());
        $this->assertDatabaseHas('kpiables', [
            'kpi_id' => $kpi->getKey(),
            'kpiable_id' => $user->getKey(),
            'kpiable_type' => User::class,
        ]);
        $this->assertDatabaseHas('kpiables', [
            'kpi_id' => $kpi->getKey(),
            'kpiable_id' => $group->getKey(),
            'kpiable_type' => ProfileGroup::class,
        ]);
        $this->assertDatabaseHas('kpiables', [
            'kpi_id' => $kpi->getKey(),
            'kpiable_id' => $position->getKey(),
            'kpiable_type' => Position::class,
        ]);
    }

    public function test_it_can_get_all_related_targets()
    {
        // fake data
        $user = User::factory()->create();
        $group = ProfileGroup::factory()->create();
        $position = Position::factory()->create();
        $kpi = Kpi::factory()->create();
        //swap
        $kpi->users()->attach($user);
        $kpi->groups()->attach($group);
        $kpi->positions()->attach($position);
        // asserts
    }
}