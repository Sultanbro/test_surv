<?php

namespace App\Service\Referral;

use App\Models\Referral\ReferralSalary;
use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\TransactionInterface;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Transaction implements TransactionInterface
{
    private PaidType $paidType;
    private ?Carbon $date = null;
    private ?User $referral = null;
    private int|float $amount = 0;
    private int $level = 1;
    private ?User $referrer = null;

    public function __construct(
        private readonly CalculateInterface $calculator
    )
    {
    }

    public function touch(User $referral, PaidType $type, int $level = 1): void
    {
        $this->referral = $referral;
        $this->referrer = $referral->referrer;
        $this->paidType = $type;
        $this->level = $level;
        $this->date = $this->date ?: now();

        if ($this->alreadyPaid()) return; // already has

        $this->calculateAmount();
        $this->addSalary();

        if ($this->paidType != PaidType::FIRST_WORK) return;
        if (!$this->referrer) return;

        $this->touch($this->referrer, $type, $level + 1);
        $this->addSalary();
    }

    private function alreadyPaid(): Model
    {
        return ReferralSalary::query()
            ->when(!in_array($this->paidType, [PaidType::ATTESTATION, PaidType::FIRST_WORK]), fn(Builder $query) => $query->where('date', $this->date->format("Y-m-d")))
            ->where('type', $this->paidType->name)
            ->where('referrer_id', $this->referrer->id)
            ->where('referral_id', $this->referral->id)
            ->first();
    }

    private function calculateAmount(): void
    {
        $this->amount = $this->calculator->calculate(
            $this->referrer,
            $this->paidType,
            $this->level
        );
    }

    private function addSalary(): Model
    {
        return ReferralSalary::query()
            ->create([
                'type' => $this->paidType->name,
                'referrer_id' => $this->referrer->getKey(),
                'referral_id' => $this->referral->getKey(),
                'date' => $this->date->format("Y-m-d"),
                'is_paid' => 0,
                'amount' => $this->amount,
            ]);
    }

    public function useDate(Carbon $date): void
    {
        $this->date = $date;
    }
}