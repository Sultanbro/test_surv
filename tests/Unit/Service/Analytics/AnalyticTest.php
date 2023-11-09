<?php

namespace Tests\Unit\Service\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Facade\Analytics\Analytics;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\ProfileGroup;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class AnalyticTest extends TenantTestCase
{

    /**
     * @throws Throwable
     */
    public function test_it_can_get_analytics()
    {
        $group = ProfileGroup::factory()->create();
        $rows = AnalyticRow::factory(5)->create([
            'date' => now()->firstOfMonth()->format("Y-m-d"),
            'group_id' => $group->getKey()
        ]);

        $cols = AnalyticColumn::factory(5)->create([
            'date' => now()->firstOfMonth()->format("Y-m-d"),
            'group_id' => $group->getKey()
        ]);

        $activity = Activity::factory()->create([
            'group_id' => $group->getKey()
        ]);

        $types = [
            'stat',
            'formula',
            'avg',
            'sum',
            'salary',
            'salary_day',
            'time',
        ];

        foreach ($cols as $key => $col) {
            foreach ($rows as $row) {
                AnalyticStat::factory()->create([
                    'type' => $types[$key],
                    'date' => now()->firstOfMonth()->format("Y-m-d"),
                    'group_id' => $group->getKey(),
                    'row_id' => $row->getKey(),
                    'column_id' => $col->getKey(),
                    'activity_id' => $activity->getKey(),
                    'value' => "[{$cols->random()->getKey()}:{$rows->random()->getKey()}] / [{$cols->random()->getKey()}:{$rows->random()->getKey()}] * 100",
                ]);
            }
        }

        $dto = new  GetAnalyticDto(
            $group->getKey(),
            now()->year,
            now()->month
        );

        /** @var Analytics $service */
        $service = app(Analytics::class);
        $service->analytics($dto);
        DB::rollBack();
    }
}
