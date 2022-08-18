<?php

namespace App\Service;

use App\Models\Analytics\Activity;
use Exception;
use Illuminate\Support\Facades\Log;

class CalculateKpiService
{

    /**
     * Получить процент выполнения по показателю
     * @param int $id
     * @return array
     */
    public function getCompletePercent(array $data, $method_id = Activity::METHOD_SUM): float
    {
        $method = Activity::getMethod($method_id);

        if(method_exists($this, $method)) {
            try {
                return $this->$method($data);
            } catch (Exception $exception) {
                Log::error($exception);
                throw new Exception($exception);
            }
        }

        throw new Exception('The kpi calculation method doesn\'t exist');
    }

    /**
     * method 
     */
    private function sum(array $data) : float
    {
        $daily_plan = (float) $data['daily_plan'];
        if($data['is_user_full_time'] == 0) {
            $daily_plan = $daily_plan / 2;
        }
        
        if($data['days_from_user_applied'] != 0) { // zero means user applied before this month
            $plan = $daily_plan * $data['days_from_user_applied'];
        } else {
            $plan = $daily_plan * $data['workdays'];
        }

        return $plan != 0 ? round($data['total_fact'] / $plan, 2) * 100 : 0.00;
    }

    /**
     * method 
     */
    private function sum_not_more(array $data) : float
    { 
        return (float)$data['daily_plan'] - $data['total_fact'] > 0 ? 100.00 : 0.00;
    }

    /**
     * method 
     */
    private function sum_not_less(array $data) : float
    {   
        return (float)$data['total_fact'] - $data['daily_plan'] >= 0 ? 100.00 : 0.00;
    }

    /**
     * method 
     */
    private function avg(array $data) : float
    { 
        if($data['records_count'] > 0) {
            $avg = $data['total_fact'] / $data['records_count'];
            $result =  $avg / ((float)$data['daily_plan']) * 100;
        } else {
            $result = 0.00;
        }

        return $result;
    }

    /**
     * method 
     */
    private function avg_not_more(array $data) : float
    { 
        if($data['records_count'] > 0 ) {
            $avg = $data['total_fact'] / $data['records_count'];
            $result =  ((float)$data['daily_plan']) / $avg * 100;
        } else {
            $result = 0.00;
        }

        return $result;
    }

    /**
     * method 
     */
    private function avg_not_less(array $data) : float
    { 
        if($data['records_count'] > 0) {
            $avg = $data['total_fact'] / $data['records_count'];
            $result =  $avg / ((float)$data['daily_plan']) * 100;
        } else {
            $result = 0.00;
        }

        return $result;
    }
}