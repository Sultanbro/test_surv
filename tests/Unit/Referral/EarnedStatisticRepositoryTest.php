<?php

namespace Tests\Unit\Referral;

use App\Enums\SalaryResourceType;
use App\Repositories\Referral\EarnedFromReferralRepository;
use App\User;
use Tests\TenantTestCase;

class EarnedStatisticRepositoryTest extends TenantTestCase
{
    public function test_it_can_get_all_earned()
    {
        $parentReferrer = User::factory()->create();
        $subReferrer = User::factory()->create([
            'referrer_id' => $parentReferrer->getKey()
        ]);
        User::factory()->create([
            'referrer_id' => $subReferrer->getKey()
        ]);

        $parentReferrer->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );

        $parentReferrer->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 2000,
            ]
        );

        $subReferrer->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );

        $repository = new EarnedFromReferralRepository();

        $result = $repository->whole($parentReferrer);

        $this->assertEquals(12000, $result);
    }

    public function test_it_can_get_referees_earned()
    {
        $referrer = User::factory()->create();
        $subReferrer1 = User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $subReferrer2 = User::factory()->create([
            'referrer_id' => $subReferrer1->getKey()
        ]);
        User::factory()->create([
            'referrer_id' => $subReferrer2->getKey()
        ]);

        $user = $referrer;
        $subUser1 = $subReferrer1;
        $subUser2 = $subReferrer2;

        $user->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );
        $user->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 5000,
            ]
        );
        $user->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 2000,
            ]
        );

        $subUser1->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );
        $subUser1->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 5000,
            ]
        );

        $subUser2->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );

        $repository = new EarnedFromReferralRepository();

        $result = $repository->fromReferees($user);

        $this->assertEquals(7000, $result);
    }

    public function test_it_can_get_earned()
    {
        $referrer = User::factory()->create();
        $subReferrer1 = User::factory(3)->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $subReferrer2 = User::factory()->create([
            'referrer_id' => $subReferrer1->first()->getKey()
        ]);
        User::factory()->create([
            'referrer_id' => $subReferrer2->getKey()
        ]);

        $referrer->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );
        $referrer->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );
        $referrer->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 10000,
            ]
        );

        $referrer->salaries()->create(
            [
                'date' => now()->format('Y-m-d'),
                'amount' => 0,
                'resource' => SalaryResourceType::REFERRAL,
                'comment_award' => "Oт реферальной ссылки",
                'award' => 5000,
            ]
        );

        $repository = new EarnedFromReferralRepository();

        $result = $repository->onlyMine($referrer);

        $this->assertEquals(3 * 10000, $result);
    }
}
