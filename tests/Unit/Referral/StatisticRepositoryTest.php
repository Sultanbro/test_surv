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
        $date = now()->format("Y-m-d");

        $referrer = User::factory()->create([
            'referrer_id' => null
        ]);

        /** @var Collection<User> $referrals */
        $referrals = User::factory(5)->create([
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
                    'date' => $date,
                ]);
                $referral->daytypes()->create([
                    'admin_id' => $referrer->getKey(),
                    'date' => $date,
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'email' => 'test@gmail.com',
                ]);
            } else {
                for ($day = 1; $day <= 6; $day++) {
                    $date = now()->subDays($day);
                    $referral->timetracking()->create([
                        'enter' => $date->subHours(8)->format("Y-m-d"),
                        'exit' => $date->format("Y-m-d"),
                        'total_hours' => 8,
                    ]);
                    ReferralSalary::factory()->create([
                        'referrer_id' => $referrer->getKey(),
                        'referral_id' => $referral->getKey(),
                        'amount' => 5000,
                        'date' => $date->format('Y-m-d'),
                        'type' => PaidType::WORK->name,
                        'is_paid' => $day % 2 === 0,
                    ]);
                    if ($day === 6) {
                        ReferralSalary::factory()->create([
                            'is_paid' => $key % 2 === 0,
                            'amount' => 10000,
                            'referral_id' => $referral->getKey(),
                            'referrer_id' => $referrer->getKey(),
                            'type' => PaidType::FIRST_WORK->name,
                            'date' => $date,
                        ]);
                        ReferralSalary::factory()->create([
                            'is_paid' => $key % 2 === 0,
                            'amount' => 5000,
                            'referral_id' => $referral->getKey(),
                            'referrer_id' => $referrer->getKey(),
                            'type' => PaidType::ATTESTATION->name,
                            'date' => $date,
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
        }
    }
}
