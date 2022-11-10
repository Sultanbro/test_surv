<?php

namespace App\Helpers;

use App\Models\Analytics\UpdatedUserStat;
use App\Service\CalculateKpiService;
use Exception;
use Illuminate\Support\Collection;

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
     * @param $kpiItems
     * @param $stat
     * @param $user
     * @return float|int
     * @throws Exception
     */
    public static function calculate($kpiItems, $stat, $user): float|int
    {
        try {
            $limits = self::checkLimits($kpiItems->kpi);
            $kpiHistory = [];
            if ($kpiItems->histories()->exists() and $kpiItems != null) {
                $kpiHistory = self::payload($kpiItems);
            }

            $amountForExecutingPlan = $kpiItems->kpi->completed_100 * self::getShare($kpiHistory['share'] ?? $kpiItems->share);

            $percent = (new CalculateKpiService)->getCompletePercent([
                'days_from_user_applied' => 0,
                'workdays' => $user->working_day_id == 1 ? 5 : 6,
                'full_time' => $user->full_time ?? 1,
                'fact' => $stat->value,
                'daily_plan' => $kpiHistory['plan'] ?? $kpiItems->plan,
                'avg' => $stat->value
            ], $kpiItems->method);

            $share = $kpiHistory['share'] ?? $kpiItems->share;

            if ($percent > $limits['lowerLimit'] && $percent < $limits['upperLimit']) {
                $amountForExecutingPlan = abs((($percent - $limits['lowerLimit']) / ($limits['upperLimit'] - $limits['lowerLimit'])) * $kpiItems->kpi->completed_80 * (self::getShare($share)));
            } else if ($percent <= $limits['lowerLimit']) {
                $amountForExecutingPlan = 0;
            }

            return $amountForExecutingPlan;

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public static function checkLimits($kpi): array
    {
        $lowerLimit = $kpi->lower_limit;
        $upperLimit = $kpi->upper_limit;

        if ($upperLimit > $lowerLimit) {
            return [
                'upperLimit' => $upperLimit,
                'lowerLimit' => $lowerLimit
            ];
        } else {
            throw new Exception('Нижний порог больше чем верхний порог');
        }
    }

    /**
     * @param $share
     * @return float|int
     */
    private static function getShare($share): float|int
    {
        return $share ? $share / 100 : 0;
    }

    /**
     * @param $kpi
     * @return mixed
     */
    private static function payload($kpi)
    {
        dump($kpi);
        $payload = $kpi->histories->first();
        dump($payload);
//        return json_decode($payload->payload, true);
    }
}