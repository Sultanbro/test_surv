<?php

namespace App\Observers\WorkChart;

use App\Models\WorkChart\WorkChartModel;
use App\User;

class WorkChartObserver
{

    /**
     * Handle the WorkChartModel "updated" event.
     *
     * @param WorkChartModel $workChartModel
     * @return void
     */
    public function updated(WorkChartModel $workChartModel)
    {
        if ($workChartModel->isDirty('work_charts_type')){
            if ($workChartModel->work_charts_type === WorkChartModel::WORK_CHART_TYPE_USUAL){
                User::where('work_chart_id', $workChartModel->id)
                ->update([
                   'first_work_day' => null
                ]);
            }
        }
    }

    /**
     * При удалении смены мы обновляем work_chart_id для всех пользователей, которые выбрали эту смену
     * @param WorkChartModel $workChartModel
     * @return void
     */
    public function deleted(WorkChartModel $workChartModel){
        User::where('work_chart_id', $workChartModel->id)->update([
           'work_chart_id' => 0
        ]);
    }
}
