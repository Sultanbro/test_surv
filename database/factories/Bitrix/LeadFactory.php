<?php

namespace Database\Factories\Bitrix;

use App\Models\Bitrix\Lead;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
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
            'lead_id' => $this->faker->numberBetween(55, 454545),
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'status' => 'NEW',
            'segment' => 5445,
            'hash' => md5(uniqid())
        ];
    }
}