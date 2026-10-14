<?php

namespace Database\Factories;

use App\Fine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fine>
 */
class FineFactory extends Factory
{

    public function definition(): array
    {
        return [
            'penalty_amount' => 400,
            'name' => 'some name'
        ];
    }
}
