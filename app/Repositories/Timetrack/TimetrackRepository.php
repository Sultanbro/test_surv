<?php

namespace App\Repositories\Timetrack;

use App\Setting;
use App\Timetracking as Model;
use App\Repositories\CoreRepository;
use App\Repositories\Interaces\TimeTrackWorkTimeInterface;
use App\Traits\TimeZoneTrait;
use App\User;
use Carbon\Carbon;

class TimetrackRepository extends CoreRepository implements TimeTrackWorkTimeInterface
{
    /**
     * Тайм зона для пользователя.
     */
    use TimeZoneTrait;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function getWorkStartTime(): string
    {
        $userWorkTime = $user->work_start ?? Model::DEFAULT_WORK_START_TIME;
        return Carbon::parse($this->getTimezone() . $userWorkTime, $this->getTimezoneSetting())->toDateString();
    }

    public function getWorkEndTime()
    {
        // TODO: Implement getWorkEndTime() method.
    }


}