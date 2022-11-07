<?php

namespace App\Repositories\Interfaces;

use App\User;
use Carbon\Carbon;

interface UpdatedUserStatRepositoryInterface
{
    public function getUpdatedStatistics(User $user, Carbon $date): int;
}