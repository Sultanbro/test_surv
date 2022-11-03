<?php

namespace App\Repositories\Interaces;

use App\User;

interface TimeTrackForTrainee
{
    /**
     * Принятие на работу стажера.
     * @param User $user
     * @return void
     */
    public function createTrainee(User $user): void;
}