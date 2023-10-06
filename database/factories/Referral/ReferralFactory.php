<?php

namespace Database\Factories\Referral;

use App\Models\Referral\Referral;
use App\Models\Referral\Referrer;
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
