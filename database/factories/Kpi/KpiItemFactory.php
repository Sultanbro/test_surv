<?php

namespace Database\Factories\Kpi;

use App\Models\Analytics\Activity;
use App\Models\Kpi\Kpi;
use App\Models\Kpi\KpiItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KpiItem>
 */
class KpiItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kpi = Kpi::factory()->create();
        $activity = Activity::factory()->create();
        return [
            'name' => 'Активность',
            'kpi_id' => $kpi->getKey(),
            'activity_id' => $activity->getKey(),
            'plan' => '500',
            'share' => '4545',
            'cell' => '545',
            'method' => '1',
            'unit' => 'fg',
        ];
    }
}
