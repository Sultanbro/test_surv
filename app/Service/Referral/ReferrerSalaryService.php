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

        $users = User::withTrashed()
            ->select(['id', 'referrer_id', 'referrer_status', 'deleted_at'])
            ->when($user, fn($query) => $query->where('id', $user->id))
            ->where(function (Builder $query) use ($to) {
                $query->whereNull('deleted_at');
                $query->orWhere('deleted_at', '>=', $to->format("Y-m-d"));
            })
            ->with(['description' => fn($query) => $query->select('user_id', 'is_trainee')])
            ->withWhereHas('referrer')
            ->get();

        $trainers = $users->filter(fn($user) => $user->description->is_trainee);
        $employees = $users->filter(fn($user) => !$user->description->is_trainee);

        while ($from <= $to) {
            dump('date: ' . $from->format("Y-m-d"));

            foreach ($trainers as $trainee) {
                Referring::touchReferrerSalaryDaily($trainee, $from);
                Referring::touchReferrerStatus($trainee->referrer);
                dump('trainee_id: ' . $trainee->id);
            }

            foreach ($employees as $employee) {
                Referring::touchReferrerSalaryWeekly($employee, $from);
                Referring::touchReferrerStatus($employee->referrer);
                dump('employee_id: ' . $employee->id);
            }

            $from->addDay();
        }
    }
}