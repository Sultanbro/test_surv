<?php

namespace App\Facade\Tariff;

use App\Exceptions\Tariff\UsersLimitExceededException;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\User;

class CurrentTariff
{
    /**
     * @throws UsersLimitExceededException
     */
    public static function ensureCanAddNewUser(): void
    {
        $tariffPlan = TariffSubscription::getValidTariffPayment();

        $userLimit = Tariff::$defaultUserLimit;

        if ($tariffPlan) {
            $userLimit = $tariffPlan->total_user_limit;
        }


        $usersCount = User::query()->count();
        if ($usersCount >= $userLimit) {
            UsersLimitExceededException::countException($userLimit);
        }
    }
}