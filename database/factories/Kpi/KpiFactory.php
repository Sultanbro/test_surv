<?php

namespace Database\Factories\Kpi;

use App\Models\Kpi\Kpi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kpi>
 */
class KpiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'completed_80' => $this->faker->numberBetween(0, 100),
            'completed_100' => $this->faker->numberBetween(0, 100),
            'lower_limit' => $this->faker->numberBetween(0, 100),
            'upper_limit' => $this->faker->numberBetween(0, 100),
            'colors' => json_encode([
                'red' => $this->faker->randomNumber(),
                'green' => $this->faker->randomNumber()
            ])
        ];
    }
}
