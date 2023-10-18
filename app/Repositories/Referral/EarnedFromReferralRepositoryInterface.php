<?php

namespace App\Repositories\Referral;

use App\User;

interface EarnedFromReferralRepositoryInterface
{
    public function whole(User $user = null): int;

    public function onlyMine(User $user = null): int;

    public function fromReferees(User $user = null): int;
}
