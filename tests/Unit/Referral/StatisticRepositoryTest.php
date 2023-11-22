<?php

namespace Tests\Unit\Referral;

use App\DayType;
use App\Models\Bitrix\Lead;
use App\Models\Referral\ReferralSalary;
use App\Repositories\Referral\StatisticRepository;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\PaidType;
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
        $repo = app(StatisticRepository::class);
        $result = $repo->statistic([]);
        dd($result);
        $this->assertArrayHasKey('pivot', $result, 'method returns the pivot statistic');
        $this->assertArrayHasKey('referrers', $result, 'method returns the referrers statistic');
        DB::rollBack();
    }

    private function seedData(): void
    {
        $date = now();
        $referrer = User::factory()->create([
            'referrer_id' => null
        ]);

        /** @var Collection<User> $referrals */
        $referrals = User::factory(15)->create([
            'referrer_id' => $referrer->getKey()
        ]);

        foreach ($referrals as $key => $referral) {
            $desc = $referral->description()->create([
                'is_trainee' => $key % 2 !== 0,
            ]);
            if ($desc->is_trainee) {
                ReferralSalary::factory()->create([
                    'is_paid' => $key % 2 === 0,
                    'amount' => 1000,
                    'referral_id' => $referral->getKey(),
                    'referrer_id' => $referrer->getKey(),
                    'type' => PaidType::TRAINEE->name,
                    'date' => now()->format("Y-m-d"),
                ]);
                $referral->daytypes()->create([
                    'admin_id' => $referrer->getKey(),
                    'date' => now()->format("Y-m-d"),
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'email' => 'test@gmail.com',
                ]);
            } else {
                for ($day = 1; $day <= 6; $day++) {
                    $date = $date->copy()->subDays($day);
                    $sub = $date->copy()->subDays($day)
                        ->subHours(8)
                        ->format("Y-m-d");
                    $referral->timetracking()->create([
                        'enter' => $sub,
                        'exit' => $date->format("Y-m-d"),
                        'total_hours' => 8,
                    ]);
                    ReferralSalary::factory()->create([
                        'referrer_id' => $referrer->getKey(),
                        'referral_id' => $referral->getKey(),
                        'amount' => 5000,
                        'date' => $date->format("Y-m-d"),
                        'type' => PaidType::WORK->name,
                        'is_paid' => $day % 2 === 0,
                    ]);
                    if ($day === 6) {
                        ReferralSalary::factory()->create([
                            'is_paid' => true,
                            'amount' => 10000,
                            'referral_id' => $referral->getKey(),
                            'referrer_id' => $referrer->getKey(),
                            'type' => PaidType::FIRST_WORK->name,
                            'date' => $date->format("Y-m-d"),
                        ]);
                        ReferralSalary::factory()->create([
                            'is_paid' => true,
                            'amount' => 5000,
                            'referral_id' => $referral->getKey(),
                            'referrer_id' => $referrer->getKey(),
                            'type' => PaidType::ATTESTATION->name,
                            'date' => $date->format("Y-m-d"),
                        ]);
                    }
                }
            }

            Lead::factory()->create([
                'referrer_id' => $referrer->getKey(),
                'user_id' => $referral->getKey(),
                'segment' => LeadTemplate::SEGMENT_ID,
                'deal_id' => 56565,
            ]);

            $subReferral = User::factory()->create([
                'referrer_id' => $referral->getKey()
            ]);

            $subReferral->description()->create([
                'is_trainee' => true
            ]);

            Lead::factory()->create([
                'referrer_id' => $referral->getKey(),
                'user_id' => $subReferral->getKey(),
                'segment' => 26,
                'deal_id' => 56565,
            ]);

            ReferralSalary::factory()->create([
                'is_paid' => $key % 2 === 0,
                'amount' => 5000,
                'referrer_id' => $referral->getKey(),
                'referral_id' => $subReferral->getKey(),
                'type' => PaidType::ATTESTATION->name,
                'date' => now()->format("Y-m-d"),
            ]);

        }
    }
}
