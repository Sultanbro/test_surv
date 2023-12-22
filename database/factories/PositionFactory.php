<?php

namespace Database\Factories;

use App\Position;
use App\ProfileGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groups = ProfileGroup::factory(3)->create();
        return [
            'book_groups' => $groups->pluck('id')->toJson(),
            'position' => 'fake positions',
            'indexation' => $this->faker->boolean, // Ведется ли индексация в течение одного года
            'sum' => $this->faker->numberBetween(0, 54545),// Сумма
            'is_head' => $this->faker->boolean,
            'is_spec' => $this->faker->boolean,
        ];
    }
}
