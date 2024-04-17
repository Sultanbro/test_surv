<?php

namespace Database\Factories;

use App\Models\CentralUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CentralUser>
 */
class CentralUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // Password placeholder
            'remember_token' => $this->faker->sha1,
            'birthday' => $this->faker->date('Y-m-d', '-20 years'),
            'city' => $this->faker->city,
            'currency' => $this->faker->currencyCode
        ];
    }
}
