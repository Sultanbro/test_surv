<?php

namespace Database\Factories\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyticColumn>
 */
class AnalyticColumnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $group = ProfileGroup::factory()->create();

        return [
            'group_id' => $group->getKey(),
            'name' => 'some name',
            'date' => now()->format('Y-m-d'),
            'order' => 1,
        ];
    }
}
