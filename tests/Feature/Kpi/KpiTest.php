<?php

namespace Feature\Kpi;

use App\Models\History;
use App\Models\Kpi\Kpi;
use App\Models\Kpi\KpiItem;
use App\Position;
use App\ProfileGroup;
use App\User;
use Tests\TenantTestCase;

class KpiTest extends TenantTestCase
{
    public function test_it_can_fetch_kpis()
    {
        $user = User::factory()->create();
        $positions = Position::factory()->create();
        $group = ProfileGroup::factory()->create();
        $kpi = Kpi::factory()->create();
        $kpi->users()->attach($user);
        $kpi->positions()->attach($positions);
        $kpi->groups()->attach($group);

        History::query()->create([
            'reference_table' => 'App\Models\Kpi\Kpi',
            'reference_id' => $kpi->id,
            'actor_id' => 5,
            'payload' => json_encode([
                'completed_80' => $kpi->completed_80 ?? null,
                'completed_100' => $kpi->completed_100 ?? null,
                'lower_limit' => $kpi->lower_limit ?? null,
                'upper_limit' => $kpi->upper_limit ?? null,
                'colors' => $kpi->colors ?? null,
                'children' => $kpi->children,
                'off_limit' => $kpi->off_limit
            ]),
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        $kpiItem = KpiItem::factory()->create([
            'kpi_id' => $kpi->getKey()
        ]);
        History::query()->create([
            'reference_table' => 'App\Models\Kpi\KpiItem',
            'reference_id' => $kpiItem->getKey(),
            'actor_id' => auth()->id() ?? 5,
            'payload' => json_encode([
                'name' => $kpiItem->name,
                'kpi_id' => $kpiItem->kpi_id,
                'activity_id' => $kpiItem->activity_id,
                'plan' => $kpiItem->plan,
                'daily_plan' => $kpiItem->activity?->daily_plan,
                'share' => $kpiItem->share,
                'cell' => $kpiItem->cell,
                'method' => $kpiItem->method,
                'unit' => $kpiItem->unit,
            ])
        ]);
        $user = User::factory()->create();
        $group = ProfileGroup::factory()->create();
        $group->editors_id = $user->pluck('id')->toJson();
        $this->actingAs($user);
        $response = $this->json('post', 'kpi/get');
        dd($response->getOriginalContent());
    }
}