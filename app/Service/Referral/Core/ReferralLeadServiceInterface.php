<?php

namespace App\Service\Referral\Core;

interface ReferralLeadServiceInterface
{
    public function create(ReferralInterface $referral, array $request): void;
}