<?php

namespace Tests\Feature\Referral;

use App\User;
use Tests\TenantTestCase;
use Throwable;

class ReferralUrlTest extends TenantTestCase
{
    /**
     * @return void
     * @throws Throwable
     */

    public function test_it_can_get_user_referral_url()
    {
        $authUser = User::factory()->create();
        $this->actingAs($authUser);
        $response = $this->json('get', 'referrals/url');
        $response->assertJsonStructure([
            'data' => [
                'url',
                'referrer_id',
            ]
        ]);
        $response->assertSeeText("job.bpartners");
    }
}
