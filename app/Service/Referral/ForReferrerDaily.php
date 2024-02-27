<?php

namespace App\Service\Referral;

use App\Facade\Referring;
use App\User;
use Illuminate\Database\Eloquent\Collection;

class ForReferrerDaily
{

    public function handle(): void
    {

        /** @var Collection<User> $trainers */
        $trainers = User::query()
            ->whereNotNull('referrer_id')
            ->whereRelation('description', 'is_trainee', 1)
            ->get();

        foreach ($trainers as $trainer) {
            Referring::touchReferrerSalaryDaily($trainer, now());
        }

        /** @var Collection<User> $employees */
        $employees = User::query()
            ->whereNotNull('referrer_id')
            ->whereRelation('description', 'is_trainee', 0)
            ->get();

        foreach ($employees as $employee) {

            $referrer = $employee->referrer;
            Referring::touchReferrerSalaryWeekly($referrer, now());
        }
    }
}