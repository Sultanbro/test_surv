<?php

namespace App\Service\Referral\Core;

use App\User;

interface StatusServiceInterface
{
    public function touch(?ReferrerInterface $user = null): User;
}