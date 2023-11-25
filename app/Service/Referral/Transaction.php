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
        $this->ensureNeedDate();

        if ($this->alreadyPaid()) return; // already has

        $this->calculateAmount();
        $this->addSalary();
    }

    private function addSalary(): void
    {
        $data = [
            'amount' => $this->amount,
            'type' => $this->paidType->name,
            'referral_id' => $this->referral->getKey(),
        ];

        if ($this->date) {
            $data['date'] = $this->date->format("Y-m-d");
        }

        $referrer = $this->referral->referrer;
        $referrer->referralSalaries()
            ->updateOrCreate($data, [
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

    private function alreadyPaid(): bool
    {
        return $this->referral->referrerSalaries()?->where('type', $this->paidType->name)->exists();
    }

    private function ensureNeedDate(): void
    {
        if ($this->paidType != PaidType::ATTESTATION->name) $this->date ?: now();
    }
}