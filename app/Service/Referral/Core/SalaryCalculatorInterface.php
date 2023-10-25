<?php
declare(strict_types=1);

namespace App\Service\Referral\Core;

interface SalaryCalculatorInterface
{
    public function calculate(ReferrerInterface $referrer): array;
}