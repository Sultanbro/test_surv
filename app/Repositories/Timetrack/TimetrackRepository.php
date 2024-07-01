<?php

namespace App\Repositories\Timetrack;

use App\Repositories\Interfaces\TimeTrackHistoryForTrainee;
use App\Setting;
use App\Timetracking as Model;
use App\Repositories\CoreRepository;
use App\Repositories\Interfaces\TimeTrackWorkTimeInterface;
use App\Traits\TimeZoneTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Получаем график сотрудника во сколько он начинает работу.
     *
     * @return string
     */
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