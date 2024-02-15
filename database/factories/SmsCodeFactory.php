<?php

namespace Database\Factories;

use App\Models\SmsCode;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SmsCode>
 */
class SmsCodeFactory extends Factory
{

    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'user_id' => $user->getKey(),
            'code' => $this->faker->numberBetween(454, 88978)
        ];
    }
}
