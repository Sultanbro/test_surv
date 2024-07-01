<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;

interface UpdatedUserStatRepositoryInterface
{
    public function retrieveLastRecordUpdatedStatisticsForEachKpi(User $user, Carbon $date);
}