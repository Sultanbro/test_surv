<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\TransactionInterface;
use App\User;
use Carbon\Carbon;

class Transaction implements TransactionInterface
{
    private PaidType $paidType;
    private ?Carbon $date = null;
    private User $referral;
    private int|float $amount;

    public function __construct(
        private readonly CalculateInterface $calculator
    )
    {
    }

    public function touch(User $referral, PaidType $type): void
    {
        $this->referral = $referral;
        $this->paidType = $type;
        $this->date = $this->date ?: now();
        $this->calculateAmount();
        $this->addSalary();
    }

    private function addSalary(): void
    {
        $referrer = $this->referral->referrer;
        $referrer->referralSalaries()
            ->updateOrCreate([
                'date' => $this->date->format("Y-m-d"),
                'amount' => $this->amount,
                'type' => $this->paidType->name,
                'referral_id' => $this->referral->getKey(),
            ], [
                'is_paid' => 0,
            ]);
    }

    public function useDate(Carbon $date): void
    {
        $this->date = $date;
    }

    private function calculateAmount(): void
    {
        $this->amount = $this->calculator->calculate($this->referral->referrer, $this->paidType);
    }
}