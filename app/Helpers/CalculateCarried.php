<?php

namespace App\Helpers;

use App\Models\Analytics\UpdatedUserStat;

class CalculateCarried
{
    public static function calculate($kpi, $stat)
    {
        $amountOfExecutingPlan = $kpi->completed_100 * ((int)$kpi->share / 100);
        if ($stat->value < $kpi->plan) {
            $amountOfExecutingPlan = $amountOfExecutingPlan * ((int) $stat->value / 100);
        }

        return $amountOfExecutingPlan;
    }
}