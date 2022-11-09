<?php

namespace App\Helpers;

use App\Models\Analytics\UpdatedUserStat;
use App\Service\CalculateKpiService;
use Exception;

class CalculateCarried
{
    /**
     * Выполнено на 100%
     */
    const COMPLETE_100 = 100.0;

    /**
     * Выполнено на 80%
     */
    const COMPLETE_80 = 80.0;

    /**
     * @param $kpi
     * @param $stat
     * @param $user
     * @return float|int
     * @throws Exception
     */
    public static function calculate($kpi, $stat, $user): float|int
    {
        dump($kpi);
        try {
            $amountOfExecutingPlan = $kpi->completed_100 * ((int)$kpi->share / 100);
            $percent =  (new CalculateKpiService)->getCompletePercent([
                'days_from_user_applied' => 0,
                'workdays'               => $user->working_day_id == 1 ? 5 : 6,
                'full_time'              => $user->full_time ?? 1,
                'fact'                   => $stat->value,
                'daily_plan'             => $kpi->plan,
                'avg'                    => $stat->value
            ], $kpi->method);

            if ($percent > self::COMPLETE_80 && $percent < self::COMPLETE_100) {
                $amountOfExecutingPlan = abs((($percent - self::COMPLETE_80) / (self::COMPLETE_100 - self::COMPLETE_80)) * $kpi->completed_100 * (self::getShare($kpi)));
            } else if ($percent < self::COMPLETE_80) {
                $amountOfExecutingPlan = 0;
            }

            return $amountOfExecutingPlan;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $kpi
     * @return float|int
     */
    private static function getShare($kpi): float|int
    {
        return $kpi ? $kpi->share / 100 : 0;
    }
}