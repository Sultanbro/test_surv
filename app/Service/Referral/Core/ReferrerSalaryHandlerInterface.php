<?php

namespace App\Service\Referral\Core;

interface ReferrerSalaryHandlerInterface
{
    public function handle(ReferrerInterface $referrer): void;
}