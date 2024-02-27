<?php

namespace App\Service\Referral;

use App\Facade\Referring;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class ReferrerSalaryService
{

    public function updateSalaries(?User $user = null): void
    {
        $from = now()->startOfMonth();
        $to = now()->endOfMonth();

        $referrals = User::withTrashed()
            ->withWhereHas('referrer')
            ->select(['id', 'referrer_id', 'referrer_status', 'deleted_at'])
            ->when($user, fn($query) => $query->where('id', $user->id))
            ->where(function (Builder $query) use ($to) {
                $query->whereNull('deleted_at');
                $query->orWhere('deleted_at', '>=', $to->format("Y-m-d"));
            })
            ->get();

        while ($from <= $to) {
            dump('date: ' . $from->format("Y-m-d"));

            foreach ($referrals as $referral) {
                Referring::touchReferrerSalaryDaily($referral, $from);
                Referring::touchReferrerSalaryWeekly($referral, $from);
                Referring::touchReferrerSalaryForCertificate($referral);
                Referring::touchReferrerStatus($referral->referrer);
                dump('user_id: ' . $referral->id);
            }

            $from->addDay();
        }
    }
}