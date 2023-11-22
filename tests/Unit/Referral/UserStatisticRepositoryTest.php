<?php

namespace Tests\Unit\Referral;

use App\DayType;
use App\Models\Bitrix\Lead;
use App\Repositories\Referral\UserStatisticRepository;
use App\Service\Referral\Core\PaidType;
use App\User;
use App\UserDescription;
use Faker\Factory;
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
        $this->actingAs($user);
        $this->seedData($user, 20);
        $repo = app(UserStatisticRepository::class);
        $result = $repo->statistic([]);
        DB::rollBack();
    }

    private function seedData(User $referrer, int $count = 4): void
    {
        // employees
        User::factory($count / 2)->create([
            'referrer_id' => $referrer->getKey()
        ])
            ->each(function (User $referral) use ($referrer) {
                /** @var UserDescription $desc */
                $referral->user_description()->create(
                    [
                        'is_trainee' => false
                    ]
                );
                $this->createSalary($referrer, $referral, PaidType::ATTESTATION);
                $this->createSalary($referrer, $referral, PaidType::FIRST_WORK);
                $this->createSalary($referrer, $referral, PaidType::WORK, 6);
                $this->createLead($referrer, $referral);
                $this->timeTracking($referral);
            });

        // trainees
        User::factory($count / 2)->create([
            'referrer_id' => $referrer->getKey()
        ])->each(function (User $referral) use ($referrer) {
            /** @var UserDescription $desc */
            $referral->user_description()->create(
                [
                    'is_trainee' => true
                ]
            );
            $this->createDayTypes($referral, $referrer, 6);
            $this->createSalary($referrer, $referral, PaidType::TRAINEE, 6);
            $this->createLead($referrer, $referral);

        });
    }

    private function createDayTypes(User $referral, User $referrer, int $count = 5): void
    {
        while ($count > 0) {
            DayType::query()
                ->create([
                    'admin_id' => $referrer->getKey(),
                    'user_id' => $referral->getKey(),
                    'date' => now()->subDays($count)->format("Y-m-d"),
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'email' => 'test@gmail.com',
                ]);
            --$count;
        }
    }

    private function createSalary(User $referrer, User $referral, PaidType $type, int $count = 1): void
    {
        while ($count > 0) {
            $referrer->referralSalaries()->create([
                'referral_id' => $referral->getKey(),
                'amount' => PaidType::getValue($type),
                'is_paid' => Factory::create()->boolean,
                'comment' => 'test comment',
                'type' => $type,
                'date' => now()->subDays($count)->format("Y-m-d"),
            ]);

            --$count;
        }
    }

    private function createLead(User $referrer, User $referral): void
    {
        Lead::factory()->create([
            'referrer_id' => $referrer->getKey(),
            'user_id' => $referral->getKey(),
        ]);
    }

    private function timeTracking(User $referral, int $count = 5): void
    {
        while ($count > 0) {
            $referral->timetracking()->create([
                'enter' => now()->subDays($count)->format("Y-m-d"),
                'exit' => now()->subDays($count)->format("Y-m-d"),
                'total_hours' => 4,
            ]);
            --$count;
        }
    }
}
