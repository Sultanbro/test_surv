<?php

namespace App\Repositories\Interfaces;
use App\Models\Analytics\Activity;

interface ActivityInterface
{
    public function getDailyPlan(Activity $activity, int $year, int $month);
}