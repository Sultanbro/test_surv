<?php

namespace Tests\Unit\Referral;

use App\Service\Referral\Core\ReferrerStatus;
use App\Service\Referral\StatusService;
use App\User;
use Tests\TenantTestCase;

class ReferralStatusServiceTest extends TenantTestCase
{
    public function test_it_can_correctly_update_user_referral_status_to_activist()
    {
        $user = User::factory()->create();

        $referrals = User::factory(5)->create([
            'referrer_id' => $user->id
        ]);

        $referrals->map(fn(User $referral) => $referral
            ->description()
            ->create([
                'applied' => now()->format('Y-m-d')
            ])
        );

        $service = new StatusService();
        $result = $service->touch($user);
        $this->assertEquals(ReferrerStatus::ACTIVIST->serialize(), $result->referrer_status);
    }

    public function test_it_can_correctly_update_user_referral_status_to_ambassador()
    {
        $user = User::factory()->create();

        $referrals = User::factory(15)->create([
            'referrer_id' => $user->id
        ]);

        $referrals->map(fn(User $referral) => $referral
            ->description()
            ->create([
                'applied' => now()->format('Y-m-d')
            ])
        );

        $service = new StatusService();
        $result = $service->touch($user);
        $this->assertEquals(ReferrerStatus::AMBASSADOR->serialize(), $result->referrer_status);
    }

    public function test_it_can_correctly_update_user_referral_status_when_user_fired()
    {
        $user = User::factory()->create();

        $referrals = User::factory(15)->create([
            'referrer_id' => $user->id
        ]);

        $referrals->map(fn(User $referral) => $referral
            ->description()
            ->create([
                'applied' => now()->format('Y-m-d')
            ])
        );

        $service = new StatusService();
        $result = $service->touch($user);
        $this->assertEquals(ReferrerStatus::AMBASSADOR->serialize(), $result->referrer_status);

        $referrals->take(3)->map(fn(User $referral) => $referral
            ->description()
            ->update([
                'fired' => now()->format('Y-m-d'),
            ])
        );

        $result = $service->touch($user);
        $this->assertNotEquals(ReferrerStatus::AMBASSADOR->serialize(), $result->referrer_status);
        $this->assertEquals(ReferrerStatus::ACTIVIST->serialize(), $result->referrer_status);
    }
}
