<?php

namespace Tests\Unit\Referral;

use App\Service\Referral\Core\SalaryCalculator;
use App\User;
use Tests\TenantTestCase;

class ReferralSalaryCalculatorTest extends TenantTestCase
{
    public function test_calculate_for_parent_referrer()
    {
        $referrer = User::factory()->create();

        $calculator = new SalaryCalculator();

        // Calculate the salary for the referrer
        $result = $calculator->calculate($referrer);

        $this->assertSame([[
            'referrer_id' => $referrer->getKey()
            , 'expected_salary' => 10000
        ]], $result);
    }

    public function test_calculate_for_multi_level_parent_referrer()
    {
        $referrer1 = User::factory()->create();
        $referrer2 = User::factory()->create([
            'referrer_id' => $referrer1->getKey()
        ]);

        $referrer3 = User::factory()->create([
            'referrer_id' => $referrer2->getKey()
        ]);

        // Create an instance of the ReferralSalaryCalculator
        $calculator = new SalaryCalculator();

        // Calculate the salary for the referrer
        $result = $calculator->calculate($referrer3);

        // Assert that the result is not an empty array because there is a parent referrer
        $this->assertCount(3, $result);
        $this->assertSame([
            [
                'referrer_id' => $referrer3->getKey()
                , 'expected_salary' => 10000
            ],
            [
                'referrer_id' => $referrer2->getKey()
                , 'expected_salary' => 5000
            ],
            [
                'referrer_id' => $referrer1->getKey()
                , 'expected_salary' => 2000
            ],
        ], $result);
    }
}
