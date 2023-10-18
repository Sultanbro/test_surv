<?php

namespace Tests\Unit\Referral;

use App\Models\Bitrix\Lead;
use App\Repositories\Referral\StatisticRepository;
use App\Salary;
use App\Service\Referral\Core\LeadTemplate;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class ReferralStatisticTest extends TenantTestCase
{

    public function test_it_cant_get_any_statistic_if_dont_has_lead()
    {
        $repository = new StatisticRepository();

        $user = User::factory()->create();
        $result = $repository->getStatistic([]);
        $this->assertEquals([
            'pivot' => [
                "employee_price" => 0,
                "deal_lead_conversion" => 0,
                "applied_deal_conversion" => 0,
                "earned" => 0,
                "paid" => 0
            ],
            'referrers' => []
        ], $result);
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_get_statistic_if_has_leads()
    {
        DB::beginTransaction();
        $repository = new StatisticRepository();
        $referrers = User::factory(2)
            ->create()
            ->map(function (User $referrer) {
                User::factory(1)->create([
                    'referrer_id' => $referrer->getKey()
                ])->map(function (User $referral) use ($referrer) {
                    $referral->description()->create([
                        'is_trainee' => 1
                    ]);
                    Lead::factory()->create([
                        'referrer_id' => $referrer->getKey(),
                        'segment' => LeadTemplate::SEGMENT_ID,
                        'user_id' => $referral->getKey()
                    ]);
                    Lead::factory()->create([
                        'referrer_id' => $referrer->getKey(),
                        'segment' => LeadTemplate::SEGMENT_ID,
                        'user_id' => $referral->getKey(),
                        'deal_id' => 454545
                    ]);
                    Salary::factory()->create([
                        'user_id' => $referrer->getKey(),
                        'award' => 10000,
                        'is_paid' => 1,
                        'date' => now()->format('Y-m-d'),
                    ]);
                    Salary::factory()->create([
                        'user_id' => $referrer->getKey(),
                        'award' => 10000,
                        'is_paid' => 0,
                        'date' => now()->format('Y-m-d'),
                    ]);
                });
                return $referrer;
            })
            ->map(function (User $referrer) {
                User::factory(2)->create([
                    'referrer_id' => $referrer->getKey()
                ])->map(function (User $referral) use ($referrer) {
                    $referral->description()->create([
                        'is_trainee' => 0,
                        'applied' => now()->format('Y-m-d')
                    ]);
                    Lead::factory(3)->create([
                        'referrer_id' => $referrer->getKey(),
                        'segment' => LeadTemplate::SEGMENT_ID,
                        'user_id' => $referral->getKey(),
                        'deal_id' => 454545,
                    ]);
                    Salary::factory(3)->create([
                        'user_id' => $referrer->getKey(),
                        'award' => 10000,
                        'is_paid' => 1,
                        'date' => now()->format('Y-m-d'),
                    ]);
                });
                return $referrer;
            });
        $result = $repository->getStatistic([]);
        DB::rollBack();
    }
}
