<?php

namespace App\Service\Referral;

use App\Facade\Referring;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class ForReferrerDaily
{

    public function handle(?User $user = null): void
    {
        $from = now()->startOfMonth()->format("Y-m-d");
        $to = now()->endOfMonth()->format("Y-m-d");

        $users = User::withTrashed()
            ->select(['id', 'referrer_id', 'referrer_status', 'deleted_at'])
            ->when($user, fn($query) => $query->where('id', $user->id))
            ->where(function (Builder $query) use ($to) {
                $query->whereNull('deleted_at');
                $query->orWhere('deleted_at', '>=', $to);
            })
            ->whereNotNull('referrer_id')
            ->with(['description' => fn($query) => $query->select('user_id', 'is_trainee')])
            ->withWhereHas('referrer')
            ->get();


        $trainers = $users->filter(fn($user) => $user->description->is_trainee);
        $employees = $users->filter(fn($user) => !$user->description->is_trainee);
        dd($trainers);

        foreach ($trainers as $trainer) {
            Referring::touchReferrerSalaryDaily($trainer, now());
        }

        foreach ($employees as $employee) {
            $referrer = $employee->referrer;
            Referring::touchReferrerSalaryWeekly($referrer, now());
        }
    }
}