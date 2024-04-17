<?php

namespace Tests\Unit\Service\Referral;

use App\Models\Referral\ReferralSalary;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\ReferrerSalaryService;
use App\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class ForReferrerDailyTest extends TenantTestCase
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function test_it_can_update_daily_updates()
    {
        DB::beginTransaction();
        $this->seedData();
        /** @var ReferrerSalaryService $service */
        $service = app(ReferrerSalaryService::class);
        $service->updateSalaries();
        $this->assertDatabaseHas('referral_salaries', [
            'amount' => 10000
        ]);
        $this->assertDatabaseHas('referral_salaries', [
            'amount' => 5000
        ]);
        $this->assertDatabaseHas('referral_salaries', [
            'amount' => 2000
        ]);
        DB::rollBack();
    }

    private function seedData(): void
    {
        $date = now();
        $referrer = User::factory()->create([
            'referrer_id' => null
        ]);

        /** @var Collection<User> $referrals */
        $referrals = User::factory(5)->create([
            'referrer_id' => $referrer->getKey()
        ]);

        foreach ($referrals as $key => $referral) {
            $referral->description()->create([
                'is_trainee' => false,
            ]);

            for ($day = 1; $day <= 6; $day++) {
                $date = $date->copy()->subDays($day);
                $sub = $date->copy()->subDays($day)
                    ->subHours(8)
                    ->format("Y-m-d");

                $referral->timetracking()->create([
                    'enter' => $sub,
                    'exit' => $date->format("Y-m-d"),
                    'total_hours' => 8 * 60,
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

            $subReferral = User::factory()->create([
                'referrer_id' => $referral->getKey()
            ]);

            $subReferral->description()->create([
                'is_trainee' => false
            ]);

            for ($day = 1; $day <= 6; $day++) {
                $date = $date->copy()->subDays($day);
                $sub = $date->copy()->subDays($day)
                    ->subHours(8)
                    ->format("Y-m-d");

                $subReferral->timetracking()->create([
                    'enter' => $sub,
                    'exit' => $date->format("Y-m-d"),
                    'total_hours' => 8 * 60,
                ]);

                ReferralSalary::factory()->create([
                    'referrer_id' => $subReferral->getKey(),
                    'referral_id' => $subReferral->getKey(),
                    'amount' => 5000,
                    'date' => $date->format("Y-m-d"),
                    'type' => PaidType::WORK->name,
                    'is_paid' => $day % 2 === 0,
                ]);
                if ($day === 6) {
                    ReferralSalary::factory()->create([
                        'is_paid' => true,
                        'amount' => 10000,
                        'referral_id' => $subReferral->getKey(),
                        'referrer_id' => $subReferral->getKey(),
                        'type' => PaidType::FIRST_WORK->name,
                        'date' => $date->format("Y-m-d"),
                    ]);
                    ReferralSalary::factory()->create([
                        'is_paid' => true,
                        'amount' => 5000,
                        'referral_id' => $subReferral->getKey(),
                        'referrer_id' => $subReferral->getKey(),
                        'type' => PaidType::ATTESTATION->name,
                        'date' => $date->format("Y-m-d"),
                    ]);
                }
            }

            ReferralSalary::factory()->create([
                'is_paid' => $key % 2 === 0,
                'amount' => 5000,
                'referrer_id' => $referral->getKey(),
                'referral_id' => $subReferral->getKey(),
                'type' => PaidType::ATTESTATION->name,
                'date' => now()->format("Y-m-d"),
            ]);

            $referral3 = User::factory()->create([
                'referrer_id' => $subReferral->getKey()
            ]);
            $referral3->description()->create([
                'is_trainee' => false
            ]);

            for ($day = 1; $day <= 6; $day++) {
                $date = $date->copy()->subDays($day);
                $sub = $date->copy()->subDays($day)
                    ->subHours(8)
                    ->format("Y-m-d");

                $referral3->timetracking()->create([
                    'enter' => $sub,
                    'exit' => $date->format("Y-m-d"),
                    'total_hours' => 8 * 60,
                ]);

                ReferralSalary::factory()->create([
                    'referrer_id' => $referral3->getKey(),
                    'referral_id' => $referral3->getKey(),
                    'amount' => 5000,
                    'date' => $date->format("Y-m-d"),
                    'type' => PaidType::WORK->name,
                    'is_paid' => $day % 2 === 0,
                ]);
                if ($day === 6) {
                    ReferralSalary::factory()->create([
                        'is_paid' => true,
                        'amount' => 10000,
                        'referral_id' => $referral3->getKey(),
                        'referrer_id' => $referral3->getKey(),
                        'type' => PaidType::FIRST_WORK->name,
                        'date' => $date->format("Y-m-d"),
                    ]);
                    ReferralSalary::factory()->create([
                        'is_paid' => true,
                        'amount' => 5000,
                        'referral_id' => $referral3->getKey(),
                        'referrer_id' => $referral3->getKey(),
                        'type' => PaidType::ATTESTATION->name,
                        'date' => $date->format("Y-m-d"),
                    ]);
                }
            }

        }
    }
}
