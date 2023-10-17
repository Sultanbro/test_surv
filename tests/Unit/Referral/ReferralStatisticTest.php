<?php

namespace Tests\Unit\Referral;

use App\Models\Bitrix\Lead;
use App\ProfileGroup;
use App\Repositories\Referral\StatisticRepository;
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
        $result = $repository->getStatistic($user);
        $this->assertEquals([], $result);
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_get_statistic_if_has_leads()
    {
        DB::beginTransaction();
        $repository = new StatisticRepository();
        $referrer = User::factory()->create();
        User::factory(5)->create([
            'referrer_id' => $referrer->id
        ])->map(function (User $user) {
            $profileGroup = ProfileGroup::factory()->create();
            $user->description()->create([
                    'is_trainee' => 1,
                    'applied' => now()->format('Y-m-d')]
            );
            $profileGroup->users()->attach($user);
            Lead::factory(5)->create(
                [
                    'segment' => LeadTemplate::SEGMENT_ID,
                    'referrer_id' => $user->getKey()
                ]
            )
                ->take(3)
                ->map(fn(Lead $lead) => $lead->update([
                    'deal_id' => 54545
                ]));
            return $user;
        })->map(function (User $user) {
            User::factory(5)->create([
                'referrer_id' => $user->id
            ])
                ->map(function (User $user) {
                    $user->description()->create([
                            'is_trainee' => 0,
                            'applied' => now()->format('Y-m-d')]
                    );
                    $profileGroup = ProfileGroup::factory()->create();
                    $profileGroup->users()->attach($user);
                    Lead::factory(5)->create(
                        [
                            'segment' => LeadTemplate::SEGMENT_ID,
                            'referrer_id' => $user->getKey()
                        ]
                    )
                        ->take(3)
                        ->map(fn(Lead $lead) => $lead->update([
                            'deal_id' => 54545
                        ]));
                    return $user;
                });;
            return $user;
        });
        $result = $repository->getStatistic([]);
        dd($result);
        $this->assertEquals([], $result);
        DB::rollBack();
    }
}
