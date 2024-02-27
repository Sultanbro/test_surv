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
            ->when($user, fn($query) => $query->where('id', $user->id))
            ->where(function (Builder $query) use ($to) {
                $query->whereNull('deleted_at');
                $query->orWhere('deleted_at', '>=', $to);
            })
            ->whereNotNull('referrer_id')
            ->with(['description' => fn($query) => $query->select('user_id', 'is_trainee')])
            ->withWhereHas('referrer')
            ->get();


        $trainers = $users->where('description.is_trainee', 1);
        $employees = $users->where('description.is_trainee', 0);
        dd($users);

        foreach ($trainers as $trainer) {
            Referring::touchReferrerSalaryDaily($trainer, now());
        }

        foreach ($employees as $employee) {
            $referrer = $employee->referrer;
            Referring::touchReferrerSalaryWeekly($referrer, now());
        }
    }
}