<?php

namespace App\Service;

class CalculateKpiService2
{
    public function calcSum($el, $kpi): float
    {
        $result = 0;
        $completed = $this->calcCompleted($el) / 100.0;

        $lower_limit = floatval($kpi['lower_limit']) / 100.0;
        $upper_limit = floatval($kpi['upper_limit']) / 100.0;
        $share = isset($el['share']) ? floatval($el['share']) / 100.0 : 0;
        $completed_80 = $kpi['completed_80'];
        $completed_100 = $kpi['completed_100'];

        if ($el['full_time'] == 0) {
            $completed_80 /= 2;
            $completed_100 /= 2;
        }

        if (!$kpi['off_limit'] && $completed > 1) $completed = 1;

        if ($completed > $lower_limit) {
            if ($completed < $upper_limit) {
                $result = $completed_80 * $share * ($completed - $lower_limit) * $upper_limit / ($upper_limit - $lower_limit);
            } else {
                $result = $completed_100 * $share * $completed;
            }
        }

        if ($result < 0) return 0;

        return $result;
    }

    private function calcCompleted($el): float
    {
        $res = 0;
        $fact = $this->number($el['fact']);
        $avg = $this->number($el['avg']);
        $plan = isset($el['plan']) ? (float)$el['plan'] : 0;
        $method = isset($el['method']) ? (int)$el['method'] : 0;

        dd_if($el['id'] === 603 && $el['kpi_id'] === 191, $el);

        switch ($method) {
            case 1:
                $res = ($fact / $plan * 100);
                break;
            case 2:
                $res = $el['percent'];
                break;
            case 3:
                $res = $plan - $fact >= 0 ? 100 : 0;
                break;
            case 4:
                $res = $plan - $avg >= 0 ? 100 : 0;
                break;
            case 5:
                $res = $fact - $plan >= 0 ? 100 : 0;
                break;
            case 6:
                $res = $avg - $plan >= 0 ? 100 : 0;
                break;
        }
        return floor($res);
    }

    private function number($value): float|int
    {
        return is_numeric($value) ? (float)$value : 0;
    }
}