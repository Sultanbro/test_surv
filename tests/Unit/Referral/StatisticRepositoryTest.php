<?php

namespace Tests\Unit\Referral;

use App\Models\Bitrix\Lead;
use App\Repositories\Referral\StatisticRepository;
use App\Salary;
use App\Service\Referral\Core\LeadTemplate;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class StatisticRepositoryTest extends TenantTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Throwable
     */
    public function test_it_can_statistics()
    {
        DB::beginTransaction();
        $this->seedData();
        User::factory()->create();
        $repo = new StatisticRepository;
        $result = $repo->getStatistic([]);
        $this->assertArrayHasKey('pivot', $result, 'method returns the pivot statistic');
        $this->assertArrayHasKey('referrers', $result, 'method returns the referrers statistic');
        DB::rollBack();
    }

    private function seedData(): void
    {
        $date = now()->format("Y-m-d");

        $referrer = User::factory()->create([
            'referrer_id' => null
        ]);

        /** @var Collection<User> $referrals */
        $referrals = User::factory(5)->create([
            'referrer_id' => $referrer->getKey()
        ]);

        foreach ($referrals as $key => $referral) {
            $referral->description()->create([
                'applied' => $date,
                'is_trainee' => 0,
            ]);
            Lead::factory()->create([
                'referrer_id' => $referrer->getKey(),
                'user_id' => $referral->getKey(),
                'segment' => LeadTemplate::SEGMENT_ID,
                'deal_id' => 56565,
            ]);

            Salary::factory()->create([
                'is_paid' => $key % 2 === 0,
                'award' => 5000,
                'comment_award' => $referral->getKey(),
                'user_id' => $referrer->getKey(),
                'date' => now()->format("Y-m-d"),
            ]);
        }
    }
}
