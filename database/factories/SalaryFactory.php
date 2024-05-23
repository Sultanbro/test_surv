<?php

namespace Database\Factories;

use App\Enums\SalaryResourceType;
use App\Salary;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Salary>
 */
class SalaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'user_id' => $user->getKey(),
            'date' => $this->faker->date,
            'amount' => 0,
            'paid' => 0,
            'bonus' => 0,
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'is_paid' => 0,
        ];
    }
}
