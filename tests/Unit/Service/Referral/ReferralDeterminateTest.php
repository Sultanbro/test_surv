<?php

namespace Tests\Unit\Service\Referral;

use App\Service\Referral\Core\ReferralDetermination;
use App\Service\Referral\Core\ReferralInterface;
use App\Service\Referral\Core\ReferrerInterface;
use App\User;
use Tests\TenantTestCase;

class ReferralDeterminateTest extends TenantTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_can_determinate_referrer_by_correct_referral_url()
    {
        $user = User::factory()->create();

        /** @var ReferrerInterface $referrer */
        $referrer = $user->asReferrer()->create();

        // Create an instance of ReferralInterface (or use an actual implementation)
        /** @var ReferralInterface $referral */
        $referral = $referrer->referral()->create();

        // Create an instance of ReferralDetermination with the real instances
        $referralDetermination = new ReferralDetermination();

        // Call the determinate method
        $result = $referralDetermination->determinate($referral);
        // Assert the result
        $this->assertEquals($referrer->id, $result->id, 'the expected referrer is the same');
    }
}
