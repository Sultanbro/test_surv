<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\ReferrerStatus;
use App\Service\Referral\Core\StatusServiceInterface;
use App\User;
use App\UserDescription;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class StatusService implements StatusServiceInterface
{
    public function touch(?ReferrerInterface $user = null): User
    {
        $user = $this->user($user);

        $user->update([
            'referrer_status' => $this->nextStatus($this->countReferrals($user))
        ]);

        return $user;
    }

    private function countReferrals(User $user): int
    {
        return $user->referrals()
            ->whereRelation('description', fn(UserDescription|Builder $query) => $query
                ->where(fn(UserDescription|Builder $query) => $query
                    ->whereNull('fired')
                    ->where('is_trainee', 0)
                    ->whereNot('applied', 0)
                )
            )
            ->count();
    }

    private function user(?User $user): User|Authenticatable|null
    {
        return $user ?: auth()->user();
    }

    private function nextStatus(int $countReferrals): string
    {
        return ReferrerStatus::fromCount($countReferrals)->serialize();
    }
}