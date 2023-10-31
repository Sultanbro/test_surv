<?php

namespace Tests\Unit\Referral;

use App\Service\Referral\CalculatorService;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\ReferrerStatus;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class CalculatorServiceTest extends TenantTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Throwable
     */
    public function test_it_can_calculate_amount_of_certification_for_referrer()
    {
        DB::beginTransaction();
        $service = new CalculatorService();
        $referrer = User::factory()->create([
            'referrer_status' => ReferrerStatus::PROMOTER->serialize()
        ]);
        User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $amount = $service->calculate($referrer, PaidType::ATTESTATION);
        $this->assertSame($amount, 5000);
        DB::rollBack();
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_calculate_amount_of_train_for_referrer()
    {
        DB::beginTransaction();
        $service = new CalculatorService();
        $referrer = User::factory()->create([
            'referrer_status' => ReferrerStatus::PROMOTER->serialize()
        ]);
        User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $amount = $service->calculate($referrer, PaidType::TRAINEE);
        $this->assertSame($amount, 1000);
        DB::rollBack();
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_calculate_amount_of_first_work_for_referrer()
    {
        DB::beginTransaction();
        $service = new CalculatorService();
        $referrer = User::factory()->create([
            'referrer_status' => ReferrerStatus::PROMOTER->serialize()
        ]);
        User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $amount = $service->calculate($referrer, PaidType::FIRST_WORK);
        $this->assertSame($amount, 10000);
        DB::rollBack();
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_calculate_amount_of_train_for_referrer_by_status_activist()
    {
        DB::beginTransaction();
        $service = new CalculatorService();
        $referrer = User::factory()->create([
            'referrer_status' => ReferrerStatus::ACTIVIST->serialize()
        ]);
        User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $amount = $service->calculate($referrer, PaidType::TRAINEE);
        $this->assertSame($amount, 1100);
        DB::rollBack();
    }

    /**
     * @throws Throwable
     */
    public function test_it_can_calculate_amount_of_train_for_referrer_by_status_Ambassador()
    {
        DB::beginTransaction();
        $service = new CalculatorService();
        $referrer = User::factory()->create([
            'referrer_status' => ReferrerStatus::AMBASSADOR->serialize()
        ]);
        User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $amount = $service->calculate($referrer, PaidType::TRAINEE);
        $this->assertSame($amount, 1120);
        DB::rollBack();
    }
}
