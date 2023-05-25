<?php

namespace App\Observers;

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
            $workChar = $user->getWorkChart();
            if ($workChar->work_charts_type === 1){
                $user->first_work_day = null;
                $user->save();
            }
        }
    }
}
