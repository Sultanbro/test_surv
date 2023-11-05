<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\ReferrerStatus;
use Exception;

class CalculatorService implements CalculateInterface
{
    /**
     * @throws Exception
     */
    public function calculate(ReferrerInterface $user, PaidType $type, int $level = 1): float|int
    {
        $actualTotal = $this->typeTotal($type);
        if ($type === PaidType::FIRST_WORK) {
            if ($level === 2) {
                $actualTotal = 5000;
            }
            if ($level === 3) {
                $actualTotal = 2000;
            }
        }

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

    /**
     * @throws Exception
     */
    private function statusPercent(ReferrerInterface $user): int
    {
        return ReferrerStatus::getPercent($user->referrer_status);
    }
}