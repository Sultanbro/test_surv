<?php

namespace App\Repositories\Interfaces;

use App\User;

interface TimeTrackHistoryForTrainee
{
    /**
     * Принятие на работу стажера.
     * @param User $user
     * @return void
     */
    public function createTrainee(User $user): void;
}