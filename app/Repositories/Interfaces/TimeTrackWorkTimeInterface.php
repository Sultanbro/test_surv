<?php

namespace App\Repositories\Interfaces;

interface TimeTrackWorkTimeInterface
{
    public function getWorkStartTime();

    public function getWorkEndTime();
}