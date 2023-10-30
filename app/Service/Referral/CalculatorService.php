<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\ReferrerStatus;

class CalculatorService implements CalculateInterface
{
    public function calculate(ReferrerInterface $user, PaidType $type): float|int
    {
        $actualTotal = $this->typeTotal($type);
        $percentFromReferrerStatus = $this->statusPercent($user);
        if ($percentFromReferrerStatus === 0) {
            return $actualTotal;
        }
        return ($actualTotal * $percentFromReferrerStatus / 100) + $actualTotal;
    }

    private function typeTotal(PaidType $type): int
    {
        return PaidType::getValue($type);
    }

    private function statusPercent(ReferrerInterface $user): int
    {
        return ReferrerStatus::getPercent($user->referrer_status);
    }
}