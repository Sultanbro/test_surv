<?php

namespace Tests\Unit\Referral;

use App\DayType;
use App\Models\Bitrix\Lead;
use App\ProfileGroup;
use App\Repositories\Referral\UserStatisticRepository;
use App\Service\Referral\Core\PaidType;
use App\User;
use App\UserDescription;
use Carbon\Carbon;
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
        $this->seedData($user, 5);
        $repo = app(UserStatisticRepository::class);
        $result = $repo->statistic([]);
        dd($result);
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
                $this->createLead($referrer, $referral);
                $this->createGroup($referral);
                $this->timeTracking($referral, 6);
                $date = now();
                $this->createSalary($referrer, $referral, PaidType::ATTESTATION, $date);
                $this->createSalary($referrer, $referral, PaidType::FIRST_WORK, $date);
                $this->createSalary($referrer, $referral, PaidType::WORK, $date->addDay(), 5);
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
            $this->createGroup($referral);
            $this->createDayTypes($referral, $referrer, 6);
            $this->createSalary($referrer, $referral, PaidType::TRAINEE, now(), 6);
            $this->createLead($referrer, $referral);
        });
    }

    private function createDayTypes(User $referral, User $referrer, int $count = 5): void
    {
        $date = now();
        while ($count > 0) {
            DayType::query()
                ->create([
                    'admin_id' => $referrer->getKey(),
                    'user_id' => $referral->getKey(),
                    'date' => $date->subDay()->format("Y-m-d"),
                    'type' => DayType::DAY_TYPES['TRAINEE'],
                    'email' => 'test@gmail.com',
                ]);
            --$count;
        }
    }

    private function createSalary(User $referrer, User $referral, PaidType $type, Carbon $date, int $count = 1): void
    {
        while ($count > 0) {
            $referrer->referralSalaries()->create([
                'referral_id' => $referral->getKey(),
                'amount' => PaidType::getValue($type),
                'is_paid' => Factory::create()->boolean,
                'comment' => 'test comment',
                'type' => $type,
                'date' => $date->subDays($count)->format("Y-m-d"),
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
                'enter' => now()->subDays($count)->setTime(9, 0),
                'exit' => now()->subDays($count)->setTime(16, 0),
                'total_hours' => 5,
            ]);

            --$count;
        }
    }

    private function createGroup(User $referral): void
    {
        $group = ProfileGroup::factory()->create();
        $referral->groups()->attach($group);
    }
}
