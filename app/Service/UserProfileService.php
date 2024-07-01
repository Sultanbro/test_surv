<?php

namespace App\Service;

use App\Repositories\Timetrack\TimetrackHistoryRepository;
use App\Repositories\Timetrack\TimetrackRepository;
use App\Repositories\UserDescriptionRepository;
use App\Traits\BitrixLead;

class UserProfileService
{
    use BitrixLead;

    /**
     * @param $user
     * @return void
     */
    public function approveForTrainee($user): void
    {
        (new UserDescriptionRepository)->setEmployee($user->id);
        (new TimetrackHistoryRepository)->createTrainee($user);
        $this->changeDeal($user);
    }
}