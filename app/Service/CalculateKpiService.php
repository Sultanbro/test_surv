<?php

namespace App\Service;

use App\Models\Analytics\Activity;
use Exception;
use Illuminate\Support\Facades\Log;

class CalculateKpiService
{

    /**
     * Расчет суммы К выдаче по результатам KPI
     */
    public function earned(
        int   $lower_limit,
        int   $upper_limit,
        float $completed_percent,
        int   $share,
        float $completed_80,
        float $completed_100,
    ): float|int
    {

        $result = 0;
        $lower_limit = $lower_limit / 100;
        $upper_limit = $upper_limit / 100;
        $completed_percent = $completed_percent / 100;
        $share = $share / 100;
        if ($completed_percent > $lower_limit) {


            if ($completed_percent < $upper_limit) {
                $result = $completed_80 * $share * ($completed_percent - $lower_limit) * $upper_limit / ($upper_limit - $lower_limit);
            } else {
                $result = $completed_100 * $share * $completed_percent;
            }
        }

        return max($result, 0);
    }

    /**
     * Получить процент выполнения по показателю
     */
    public function getCompletePercent(array $data, $method_id = Activity::METHOD_SUM): float
    {
        $method = Activity::getMethod($method_id);

        if (method_exists($this, $method)) {
            try {
                return $this->$method($data);
            } catch (Exception $exception) {
                Log::error($exception);
                throw new Exception($exception);
            }
        }

        throw new Exception('The kpi calculation method doesn\'t exist');
    }

    public function calcCompleted(array $data, $method = Activity::METHOD_SUM): float
    {
        $res = 0;

        $fact = $data['fact'];
        $avg = $data['avg'];
        $plan = $data['daily_plan'];

        if ($method == 1) {
            $res = number_format($fact / $plan * 100, 2);
        }

        if ($method == 2) {
            $res = $data['percent'];
        }

        if ($method == 3) {
            $res = $plan - $fact >= 0 ? 100 : 0;
        }

        if ($method == 4) {
            $res = $plan - $avg >= 0 ? 100 : 0;
        }

        if ($method == 5) {
            $res = $fact - $plan >= 0 ? 100 : 0;
        }

        if ($method == 6) {
            $res = $avg - $plan >= 0 ? 100 : 0;
        }

        return (float)$res;
    }

    /**
     * method
     */
    private function sum(array $data): float
    {
        $plan = (float)$data['daily_plan'];
        $percent = 1;

        if ($data['days_from_user_applied'] != 0) { // zero means user applied before this month
            $percent = $data['workdays'] > 0 ? $data['days_from_user_applied'] / $data['workdays'] : 1;
        }

        if ($data['full_time'] == 0) {
            $percent = $percent / 2;
        }

        return $plan != 0 ? round($data['fact'] / ($plan * $percent) * 100, 2) : 0.00;
    }

    /**
     * method
     */
    private function sum_not_more(array $data): float
    {
        return (float)$data['daily_plan'] - $data['fact'] >= 0 ? 100.00 : 0.00;
    }

    /**
     * method
     */
    private function sum_not_less(array $data): float
    {
        return (float)$data['fact'] - $data['daily_plan'] >= 0 ? 100.00 : 0.00;
    }

    /**
     * method
     */
    private function avg(array $data): float
    {
        return $data['avg'] / ((float)$data['daily_plan']) * 100;
    }

    /**
     * method
     */
    private function avg_not_more(array $data): float
    {
        return (float)$data['daily_plan'] - (float)$data['avg'] >= 0 ? 100.00 : 0.00;
    }

    /**
     * method
     */
    private function avg_not_less(array $data): float
    {
        return (float)$data['avg'] - (float)$data['daily_plan'] >= 0 ? 100.00 : 0.00;
    }


}