<?php

namespace Tests\Unit\Referral;

use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\ReferrerStatus;
use App\Service\Referral\Transaction;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;
use Throwable;

class TransactionServiceTest extends TenantTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     * @throws Throwable
     */
    public function test_it_can_create_correct_salary_for_referrer()
    {
        DB::beginTransaction();
        $referrer = User::factory()->create([
            'referrer_status' => ReferrerStatus::PROMOTER->serialize()
        ]);

        $referral = User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);

        $calculator = app(CalculateInterface::class);
        $transaction = new Transaction($calculator);

        // Act
        $transaction->touch($referral, PaidType::ATTESTATION); // Replace PaidType::SOME_TYPE with the actual paid type.

        // Assert
        $this->assertDatabaseHas('referral_salaries', [
            'referrer_id' => $referrer->getKey(),
            'referral_id' => $referral->getKey(),
            'amount' => 5000,
            'is_paid' => 0,
            'date' => now()->format("Y-m-d"),
        ]);
        DB::rollBack();
    }
}
