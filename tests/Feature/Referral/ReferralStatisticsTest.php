<?php

namespace Feature\Referral;

use App\User;
use Tests\TenantTestCase;

class ReferralStatisticsTest extends TenantTestCase
{
    public function test_it_can_get_user_statistics()
    {
        $authUser = User::factory()->create();
        $randomUser = User::factory()->create([
            'name' => 'random_user'
        ]);
        $this->actingAs($authUser);
        $response = $this->json('get', "referrals/statistics/user/{$randomUser->getKey()}");
        $response->assertJsonStructure([
            'data' => [
                'tops',
                'referrals',
                'mine',
                'from_referrals',
                'absolute',
            ]
        ]);
    }

    public function test_it_can_get_auth_user_statistics()
    {
        $authUser = User::factory()->create();
        $this->actingAs($authUser);
        $response = $this->json('get', "referrals/statistics/user");
        $response->assertJsonStructure([
            'data' => [
                'tops',
                'referrals',
                'mine',
                'from_referrals',
                'absolute',
            ]
        ]);
    }

    public function test_it_can_get_referrers_statistics()
    {
        $authUser = User::factory()->create();
        $this->actingAs($authUser);
        $response = $this->json('get', "referrals/statistics/");
        $response->assertJsonStructure([
            'data' => [
                'pivot',
                'referrers',
            ]
        ]);
    }
}
