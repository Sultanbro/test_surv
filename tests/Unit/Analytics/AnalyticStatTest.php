<?php

namespace Tests\Unit\Analytics;

use App\Facade\Analytics\Analytics;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\ProfileGroup;
use Illuminate\Database\Eloquent\Collection;
use Tests\TenantTestCase;

class AnalyticStatTest extends TenantTestCase
{
    public function test_convert_cell_coordinates_to_formula(): void
    {
        $stats = new Collection();
        $group = ProfileGroup::factory()->create();
        $rows = AnalyticRow::factory(10)->create([
            'group_id' => $group->getKey()
        ]);
        $columns = AnalyticColumn::factory(10)->create([
            'group_id' => $group->getKey()
        ]);

        foreach ($columns as $column) {
            foreach ($rows as $row) {
                $stats->add(AnalyticStat::factory()->create([
                    'row_id' => $row->getKey(),
                    'column_id' => $column->getKey(),
                    'group_id' => $group->getKey(),
                ]));
            }
        }

        /** @var Analytics $service */
        $service = app(Analytics::class);
        $result = $service->convertCellFormulaToCoordinates($stats->first(), 'I5*8', '*8');
        dd($result);
    }
}