<?php

namespace App\Repositories\Referral;

use App\User;
use Carbon\Carbon;

interface ScheduleRepositoryInterface
{
    public function schedule(User $referrer, int $step = 1);

    public function setStartDate(Carbon $date): void;

    public function setEndDate(Carbon $date): void;
}