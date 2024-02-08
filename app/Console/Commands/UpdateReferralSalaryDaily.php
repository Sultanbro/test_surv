<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UpdateReferralSalaryDaily extends Command
{
    protected $signature = 'touch:referrals {date?}';

    protected $description = 'Обнавлять расчеть начислений для рефералов';

    function handle(): void
    {
        $date = $this->argument('date') ?? now();
        $from = $date->startOfMonth();
        $to = $date->endOfMonth();

        $users = User::query()
            ->where('referrer_id', null)
            ->whereHas('referralsLeads')
            ->with(['referrals' => function (HasMany $query) {
                $query->with(['referrals' => function (HasMany $query) {
                    $query->with('referrals');
                }]);
            }])
            ->get();

        foreach ($users as $user) {

        }
//        Referring
    }
}
