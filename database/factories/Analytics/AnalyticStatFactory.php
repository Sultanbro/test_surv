<?php

namespace Database\Factories\Analytics;

use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<AnalyticStat>
 */
class AnalyticStatFactory extends Factory
{

    public function definition(): array
    {
        $group = ProfileGroup::factory()->create();
        $row = AnalyticRow::factory()->create([
            'group_id' => $group->getKey()
        ]);
        $col = AnalyticColumn::factory()->create([
            'group_id' => $group->getKey()
        ]);
        $activity = Activity::factory()->create([
            'group_id' => $group->getKey()
        ]);
        $types = [
            'stat',
            'avg',
            'sum',
            'formula',
            'salary',
            'salary_day',
            'time',
        ];
        return [
            'row_id' => $row->getKey(),
            'column_id' => $col->getKey(),
            'date' => now()->format("Y-m-d"),
            'group_id' => $group->getKey(),
            'value' => 'some value',
            'show_value' => 'some show value',
            'activity_id' => $activity->getKey(),
            'type' => Arr::random($types),
        ];
    }
}
