<?php

namespace Tests\Service\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\ProfileGroup;
use App\Service\Analytics\CreatePivotAnalyticsInterface;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class CreatePivotAnalyticsTest extends TenantTestCase
{
    /**
     * @throws Throwable
     */
    public function test_it_can_create_analytics_for_current_month(): void
    {
        DB::beginTransaction();
        $group = ProfileGroup::factory()->create([
            'active' => true,
            'has_analytics' => true
        ]);
        $rows = [];
        $columns = [];
        $stats = [];
        $date = now();
        $daysInMonth = $date->copy()
            ->subDay()
            ->daysInMonth;

        for ($day = 1; $day <= $daysInMonth, $day++;) {

            $rows[] = $column = AnalyticRow::factory()->create([
                'group_id' => $group->getKey(),
                'date' => $date->copy()->day($day)
                    ->format("Y-m-d"),
            ]);

            $columns[] = $row = AnalyticColumn::factory()->create([
                'group_id' => $group->getKey(),
                'date' => $date->copy()->day($day)
                    ->format("Y-m-d"),
            ]);

            $stats[] = AnalyticStat::factory()->create([
                'group_id' => $group->getKey(),
                'row_id' => $row->getKey(),
                'column_id' => $column->getKey(),
            ]);
        }

        /** @var CreatePivotAnalyticsInterface $service */
        $service = app(CreatePivotAnalyticsInterface::class);
        $service->create();
        DB::rollBack();
    }
}
