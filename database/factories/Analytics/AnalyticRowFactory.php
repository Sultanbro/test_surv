<?php

namespace Database\Factories\Analytics;

use App\Models\Analytics\AnalyticRow;
use App\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyticRow>
 */
class AnalyticRowFactory extends Factory
{
    public function definition(): array
    {
        $group = ProfileGroup::factory()->create();

        return [
            'group_id' => $group->getKey(),
            'name' => $this->faker->name,
            'order' => 2,
            'date' => now()->format('Y-m-d'),
            'depend_id' => null,
        ];
    }
}
