<?php

namespace App\Service\Referral\Core;

interface SalaryHandlerInterface
{
    public function handle(ReferrerInterface $referrer): void;
}