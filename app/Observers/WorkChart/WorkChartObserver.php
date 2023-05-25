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
            if ($workChartModel->work_charts_type === 1){
                User::where('work_chart_id', $workChartModel->id)
                ->update([
                   'first_work_day' => null
                ]);
            }
        }
    }
}
