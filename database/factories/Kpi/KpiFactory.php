<?php

namespace Database\Factories\Kpi;

use App\Models\Kpi\Kpi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class KpiFactory extends Factory
{
    protected $model = Kpi::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id'              => $this->faker->unique(),
            'targetable_id'   => $this->faker->numberBetween(1, 20),
            'targetable_type' => ['App\User', 'App\ProfileGroup', 'App\Position'],
            'completed_80'    => $this->faker->numberBetween(0, 100),
            'completed_100'   => $this->faker->numberBetween(0, 100),
            'lower_limit'     => $this->faker->numberBetween(0, 100),
            'upper_limit'     => $this->faker->numberBetween(0, 100),
            'colors'          => json_encode([
                'red'   => $this->faker->randomNumber(),
                'green' => $this->faker->randomNumber()
            ])
        ];
    }
}
