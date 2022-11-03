<?php

namespace App\Repositories\Timetrack;

use App\Repositories\Interaces\TimeTrackForTrainee;
use App\Setting;
use App\Timetracking as Model;
use App\Repositories\CoreRepository;
use App\Repositories\Interaces\TimeTrackWorkTimeInterface;
use App\Traits\TimeZoneTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TimetrackRepository extends CoreRepository implements TimeTrackWorkTimeInterface, TimeTrackForTrainee
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
     * Принятие на работу стажера.
     * @param User $user
     * @return void
     */
    public function createTrainee(User $user): void
    {
        $this->model()->create([
            'author_id' => Auth::user()->id,
            'author' => Auth::user()->name .' '. Auth::user()->last_name,
            'user_id' => $user->id,
            'description' => 'Принятие на работу стажера',
            'date' => date('Y-m-d')
        ]);
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