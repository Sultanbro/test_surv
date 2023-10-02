<?php

namespace Database\Factories\User\Referral;

use App\Models\User\Referral\Referrer;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Referrer>
 */
class ReferrerFactory extends Factory
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
              'user_id' => $user->getKey()
            , 'parent_referrer_id' => null
        ];
    }
}
