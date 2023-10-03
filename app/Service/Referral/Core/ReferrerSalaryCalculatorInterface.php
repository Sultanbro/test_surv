<?php
declare(strict_types=1);

namespace App\Service\Referral\Core;

interface ReferrerSalaryCalculatorInterface
{
    public function calculate(ReferrerInterface $referrer): array;
}