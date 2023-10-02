<?php

namespace Tests\Unit;

use App\Models\User\Referral\Referrer;
use App\Service\Referral\ReferralSalaryCalculator;
use App\Service\Referral\ReferrerInterface;
use App\Service\Referral\ReferrerLevel;
use App\User;
use Tests\TenantTestCase;

class ReferralSalaryCalculatorTest extends TenantTestCase
{
    public function test_it_can_calculate_salary_for_referrer_by_provided_referral_url()
    {
        // Create an instance of ReferralCalculatorInterface (or use an actual implementation)
        $calculator = new ReferralSalaryCalculator;

        /** @var ReferrerInterface $referrer */
        $referrer = Referrer::factory()->create();

        $result = $calculator->calculate($referrer);
        $this->assertIsArray($result);
        $this->assertArrayHasKey(
            $referrer->id
            , $result
            , 'The key of result should be id of referrer, in this case only 1 level');
        $this->assertEquals(current($result), ReferrerLevel::FIRST->value);
    }

    public function test_it_can_calculate_salary_for_multi_level_referrers_by_provided_referral_url()
    {
        // Create an instance of ReferralCalculatorInterface (or use an actual implementation)
        $calculator = new ReferralSalaryCalculator;
        $user = User::factory()->create();
        /** @var ReferrerInterface $referrer */
        $referrer1 = Referrer::factory()->create([
            'user_id' => $user->getKey()
        ]);
        /** @var ReferrerInterface $referrer2 */
        $referrer2 = $referrer1->referees()->create([
            'user_id' => $user->getKey()
        ]);
        /** @var ReferrerInterface $referrer3 */
        $referrer3 = $referrer2->referees()->create([
            'user_id' => $user->getKey()
        ]);
        $result = $calculator->calculate($referrer3);
        $this->assertIsArray($result);
        $this->assertArrayHasKey(
            $referrer3->id
            , $result
            , 'The key of result should be id of referrer, in this case only 1 level');
        $this->assertEquals(current($result), ReferrerLevel::FIRST->value);
    }
}
