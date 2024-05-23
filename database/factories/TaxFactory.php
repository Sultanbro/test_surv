<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tax>
 */
class TaxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->randomDigit,
            'name' => $this->faker->word,
            'amount' => $this->faker->randomDigit,
            'percent' => $this->faker->numberBetween(0, 100)
        ];
    }
}
