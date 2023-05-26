<?php

namespace App\Observers\Timetracking;

use App\Models\WorkChart\WorkChartModel;
use App\Timetracking;
use Carbon\Carbon;
use App\User;

class TimetrackingObserver
{
    /**
     * Handle the Timetracking "created" event.
     *
     * @param  \App\Timetracking  $timetracking
     * @return void
     */
    public function created(Timetracking $timetracking)
    {
        $user = User::find($timetracking->user_id);
        if ($user && empty($user->first_work_day)) {
            $workChar = $user->getWorkChart();
            if ($workChar->work_charts_type === WorkChartModel::WORK_CHART_TYPE_REPLACEABLE) {
                $user->first_work_day = Carbon::now();
                $user->save();
            }
        }

    }
}
