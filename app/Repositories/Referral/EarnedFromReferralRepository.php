<?php

namespace App\Repositories\Referral;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;

class EarnedFromReferralRepository implements EarnedFromReferralRepositoryInterface
{
    public function whole(User $user = null): int
    {
        $user = $this->eagerLoad($user);
        return $user->referralBonuses
            ->sum('award');
    }

    public function onlyMine(User $user = null): int
    {
        $user = $this->eagerLoad($user);
        return $user->referralBonuses
            ->where('award', 10000)
            ->sum('award');
    }

    public function fromReferees(User $user = null): int
    {
        $user = $this->eagerLoad($user);
        return $user->referralBonuses
            ->where('award', '<', 10000)
            ->sum('award');
    }

    private function eagerLoad(User $user = null): Authenticatable|User
    {
        $user = $user ?? auth()->user();
        return $user->load(['referralBonuses']);
    }
}
