<?php

namespace Tests\Unit;

use App\Service\Referral\Core\ReferralDto;
use App\Service\Referral\Core\ReferralGenerator;
use App\User;
use Tests\TenantTestCase;

class ReferralGeneratorTest extends TenantTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_it_can_generate_correct_referral_url()
    {
        $user = User::factory()->create();
        $asReferrer = $user->asReferrer()->firstOrCreate();
        $generator = new ReferralGenerator();
        $referral = $generator->generate($user);
        // Assert that the returned object is an instance of ReferralDto
        $this->assertInstanceOf(ReferralDto::class, $referral);
        $this->assertEquals($referral->referrer_id, $asReferrer->getKey());
        $this->assertIsString($referral->url);
    }
}
