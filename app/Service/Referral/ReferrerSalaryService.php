<?php

namespace App\Service\Referral;

use App\Facade\Referring;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReferrerSalaryService
{

    public function updateSalaries(?string $date = null, ?User $user = null): void
    {
        $from = Carbon::parse($date ?? now())->startOfMonth();
        $to = Carbon::parse($date ?? now())->endOfMonth();

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
            foreach ($referrals as $referral) {
                Referring::touchReferrerSalaryDaily($referral, $from);
                Referring::touchReferrerSalaryWeekly($referral, $from);
                Referring::touchReferrerStatus($referral->referrer);
//                dump([
//                    'date:' => $from->format("Y-m-d"),
//                    'referral_id' => $referral->id
//                ]);
                $from->addDay();
            }
        }
    }
}