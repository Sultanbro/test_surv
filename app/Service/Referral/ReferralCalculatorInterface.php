<?php
declare(strict_types=1);

namespace App\Service\Referral;

interface ReferralCalculatorInterface
{
    public function calculate(ReferrerInterface $referrer): array;
}