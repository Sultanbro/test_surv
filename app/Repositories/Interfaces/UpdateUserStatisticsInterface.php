<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;

interface UpdateUserStatisticsInterface
{
    public function getUpdatedStatistics(User $user, Carbon $date): int;
}