<?php

namespace App\Service\Referral\Core;

interface CalculateInterface
{
    public function calculate(ReferrerInterface $user, PaidType $type,int $level = 1): float|int;
}