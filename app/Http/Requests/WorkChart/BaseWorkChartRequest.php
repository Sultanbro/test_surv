<?php

namespace App\Http\Requests\WorkChart;

use App\Models\WorkChart\WorkChartModel;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class BaseWorkChartRequest extends FormRequest
{ 
    private static int $MAX_CHART_DAYS_USUAL = 7;

    /**
     * Get the validated data from the request.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null) {
        return parent::validated($key, $default);
    }


    protected function getUsualScheduleRule()
    {
        return $this->input('work_charts_type') == WorkChartModel::WORK_CHART_TYPE_USUAL ? 'required|string|max:7' : 'nullable';
    }

    protected function getChartWorkdaysRule()
    {
        return $this->input('work_charts_type') == WorkChartModel::WORK_CHART_TYPE_REPLACEABLE ? 'required' : 'nullable';
    }

    protected function getChartDayoffsRule()
    {
        return $this->input('work_charts_type') == WorkChartModel::WORK_CHART_TYPE_REPLACEABLE ? 'required' : 'nullable';
    }

    protected function getWorkChartTypeRule(){
        if ($this->input('chart_workdays') + $this->input('chart_dayoffs') > WorkChartModel::MAX_CHART_DAYS_REPLACEABLE){
            throw new BadRequestHttpException('Количество не может быть больше ' . WorkChartModel::MAX_CHART_DAYS_REPLACEABLE . ' дней');
        }
        return 'integer|exists:work_chart_type_rbs,id|in:1,2';
    }
}
