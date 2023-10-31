<?php

namespace App\Service\Referral\Core;

interface CalculateInterface
{
    public function calculate(ReferrerInterface $user, PaidType $type): float|int;
}