<?php

namespace Database\Factories\User\Referral;

use App\Models\User\Referral\Referral;
use App\Models\User\Referral\Referrer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Referral>
 */
class ReferralFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $referrer = Referrer::factory()->create();
        return [
              'referral_id' => $referrer->getKey()
            , 'token' => Str::uuid()
        ];
    }
}
