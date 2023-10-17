<?php

namespace App\Service\Referral\Core;

interface LeadServiceInterface
{
    public function create(ReferrerInterface $referrer, RequestDto $request): void;
}