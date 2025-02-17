<?php

namespace App\Service\Referral;

use App\Facade\Referring;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReferrerSalaryService
{
    public function updateSalaries(?string $date = null, ?int $userId = null): void
    {
        $date = Carbon::parse($date ?: now());

        $from = $date->copy()->startOfMonth();
        $to = $date->isCurrentDay() ? $date->copy() : $date->copy()->endOfMonth();

        $referrals = User::withTrashed()
            ->withWhereHas('referrer')
            ->select(['id', 'referrer_id', 'referrer_status', 'deleted_at'])
            ->when($userId, fn($query) => $query->where('id', $userId))
            ->where(function (Builder $query) use ($to) {
                $query->whereNull('deleted_at');
                $query->orWhere('deleted_at', '>=', $to->format("Y-m-d"));
            })
            ->get();

        while ($from <= $to) {
            foreach ($referrals as $referral) {
                Referring::salaryForTraining($referral, $from);
                Referring::salaryForWeek($referral, $from);
                Referring::syncReferrerStatus($referral->referrer);
                dump([
                    'date:' => $from->format("Y-m-d"),
                    'referral_id' => $referral->id
                ]);
            }

            $from->addDay();
        }
    }
}