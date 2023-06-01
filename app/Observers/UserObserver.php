<?php

namespace App\Observers;

use App\Models\WorkChart\WorkChartModel;
use App\User;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {
        if ($user->isDirty('work_chart_id')) {
            $workChart = $user->getWorkChart();
            if ($workChart->work_charts_type === WorkChartModel::WORK_CHART_TYPE_USUAL){
                $user->first_work_day = null;
                $user->save();
            }
        }
    }
}
