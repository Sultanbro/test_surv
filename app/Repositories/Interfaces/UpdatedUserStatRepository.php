<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;

interface UpdatedUserStatRepository
{
    public function getUpdatedStatistics(User $user, Carbon $date): int;
}