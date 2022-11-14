<?php

namespace App\Observers;

use App\Models\Analytics\UpdatedUserStat;
use App\Service\UpdatedUserStatService;
use App\Traits\SavedKpiTrait;
use Exception;

class UpdateUserStatObserver
{
    use SavedKpiTrait;

    public function __construct(public UpdatedUserStatService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the UpdateUserStat "created" event.
     *
     * @param UpdatedUserStat $updateUserStat
     * @return void
     * @throws Exception
     */
    public function created(UpdatedUserStat $updateUserStat)
    {
        dd('created');
        $this->updateOrCreate($updateUserStat);
    }

    /**
     * Handle the UpdateUserStat "updated" event.
     *
     * @param UpdatedUserStat $updateUserStat
     * @return void
     * @throws Exception
     */
    public function updated(UpdatedUserStat $updateUserStat)
    {
        dd('updated');
        $this->updateOrCreate($updateUserStat);
    }
}
