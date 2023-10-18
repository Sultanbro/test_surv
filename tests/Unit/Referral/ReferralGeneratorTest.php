<?php

namespace Tests\Unit\Referral;

use App\Service\Referral\Core\Generator;
use App\Service\Referral\Core\ReferralUrlDto;
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
        $generator = new Generator();
        $referral = $generator->generate($user);
        // Assert that the returned object is an instance of ReferralDto
        $this->assertInstanceOf(ReferralUrlDto::class, $referral);
        $this->assertIsString($referral->url);
    }
}
