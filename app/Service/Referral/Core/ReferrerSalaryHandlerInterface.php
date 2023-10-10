<?php

namespace App\Service\Referral\Core;

interface ReferrerSalaryHandlerInterface
{
    public function apply(ReferrerInterface $referrer): void;
}