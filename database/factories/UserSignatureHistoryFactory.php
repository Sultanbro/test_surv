<?php

namespace Database\Factories;

use App\Models\UserSignatureHistory;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSignatureHistory>
 */
class UserSignatureHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'contract_number' => (string)$this->faker->numberBetween(122, 78888),
            'password_number' => (string)$this->faker->numberBetween(122, 78888)
        ];
    }
}
