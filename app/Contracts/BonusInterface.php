<?php

namespace App\Contracts;

use App\User;
use Carbon\Carbon;

interface BonusInterface
{
    public function getUserBonuses(Carbon $date, User $user): array;

}