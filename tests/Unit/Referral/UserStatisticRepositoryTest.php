<?php

namespace Tests\Unit\Referral;

use App\Models\Bitrix\Lead;
use App\Repositories\Referral\UserStatisticRepository;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class UserStatisticRepositoryTest extends TenantTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Throwable
     */
    public function test_it_can_get_tops()
    {
        DB::beginTransaction();
        $user = User::factory()->create([
            'referrer_id' => null
        ]);

        $anotherUser = User::factory()->create([
            'name' => 'check this'
        ]);
        $this->actingAs($user);
        $this->seedData($user);
        $repo = app(UserStatisticRepository::class);
        $result = $repo->statistic([]);
        dd($result);
        DB::rollBack();
    }

    private function seedData($referrer): void
    {
        /** @var Collection<User> $referrals */
        $referrals = User::factory(5)->create([
            'referrer_id' => $referrer->getKey()
        ]);

        $anotherReferral = User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);

        $anotherReferral->description()->create([
            'is_trainee' => false,
        ]);

        foreach ($referrals as $referral) {
            Lead::factory()->create([
                'referrer_id' => $referrer->getKey(),
                'user_id' => $referral->getKey(),
            ]);
            $referral->description()->create([
                'is_trainee' => false,
            ]);

            $subs = User::factory(5)->create([
                'referrer_id' => $referrer->getKey()
            ]);

            foreach ($subs as $subReferral) {
                $subReferral->description()->create([
                    'is_trainee' => false,
                ]);
                User::factory(5)->create([
                    'referrer_id' => $referrer->getKey()
                ]);
            }
        }
    }
}
