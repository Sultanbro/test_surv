<?php

namespace Database\Factories\Referral;

use App\Models\Referral\ReferralSalary;
use App\Service\Referral\Core\PaidType;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReferralSalary>
 */
class ReferralSalaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $referrer = User::factory()->create();
        $referral = User::factory()->create();
        return [
            'referrer_id' => $referrer->getKey(),
            'referral_id' => $referral->getKey(),
            'date' => $this->faker->date,
            'amount' => 5000,
            'is_paid' => 0,
            'type' => PaidType::ATTESTATION->name,
        ];
    }
}
