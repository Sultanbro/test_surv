<?php

namespace App\Observers\Timetracking;

use App\Models\WorkChart\WorkChartModel;
use App\Timetracking;
use App\User;
use Carbon\Carbon;

class TimetrackingObserver
{
    /**
     * Handle the Timetracking "created" event.
     *
     * @param \App\Timetracking $timetracking
     * @return void
     */
    public function created(Timetracking $timetracking)
    {
        $user = User::query()->find($timetracking->user_id);
        if (!$user) return;

        if (empty($user->first_work_day)) {
            $workChart = $user->getWorkChart();
            if ($workChart && $workChart->work_charts_type === WorkChartModel::WORK_CHART_TYPE_REPLACEABLE) {
                $user->first_work_day = Carbon::now()->format("Y-m-d 00:00:00");
                $user->save();
            }
        }
    }
}
