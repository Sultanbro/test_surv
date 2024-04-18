<?php

namespace App\Service\Timetrack;

use App\Fine;
use App\Setting;
use App\Timetracking;
use App\TimetrackingHistory;
use App\User;
use App\UserFine;
use Carbon\Carbon;

class UserLateService
{
    private bool $hasFineLessThanFiveMinutes;
    private bool $hasFineMoreThanFiveMinutes;
    private User $user;

    public function __construct(
        private readonly Carbon $date
    )
    {

    }

    /**
     * @throws \Exception
     */
    public function addUserFineIfLate(User $user): void
    {
        $this->user = $user;
        // Был ли штраф на ту дату, которую передаем.
        $this->isUserHasFines();

        // Отнимаем $user->timezone часов так как время сервера GTM +0.
        // Время начала смены для сотрудника.
        $shouldStartTime = $this->getWorkDayShouldStartTime();

        // Получаем запись из timetracking таблицы.
        $actualTime = $this->getWorkDayActualStartedTime();

        if (!$actualTime) return;
        //Разница в минутах.
        $diffInMinutes = $shouldStartTime->diffInMinutes($actualTime,false);

        dd($diffInMinutes);
        // Если минута 0 или меньше 0, то сотрудник пришел вовремя.
        if ($diffInMinutes <= 0) return;

        $userFine = new UserFine;

        if ($diffInMinutes <= 5) {
            if ($this->hasFineLessThanFiveMinutes) return;
            //Создаем штраф менее 5 минут.
            $userFine->fine_id = Fine::TYPE_LATE_LESS_5;
            $this->addHistory('За приход на работу с опозданием до 5 минут');
        }

        if ($diffInMinutes > 5) {
            if ($this->hasFineMoreThanFiveMinutes) return;
            //Штраф после 5 минут.
            $userFine->fine_id = Fine::TYPE_LATE_MORE_5;
            $this->addHistory('За приход на работу с опозданием от 5 минут и более');
        }

        $userFine->addUserFine([
            'user_id' => $this->user->id,
            'day' => $this->date,
            'status' => UserFine::STATUS_ACTIVE,
            'note' => 'check this'
        ]);
    }

    private function addHistory($message): void
    {

        $th = TimetrackingHistory::query()->whereDate('date', $this->date)
            ->where('user_id', $this->user->id)
            ->where('description', 'like', $message)
            ->first();

        if (!$th) {
            TimetrackingHistory::query()->create([
                'user_id' => $this->user->id,
                'author_id' => 5,
                'author' => 'Система',
                'date' => $this->date,
                'description' => $message,
            ]);
        }
    }

    private function getWorkDayActualStartedTime(): ?Carbon
    {
        $time = Timetracking::query()
            ->where('user_id', $this->user->id)
            ->whereDate('enter', $this->date)
            ->min('enter');
        if (!$time) return null;

        return Carbon::createFromTimeString(
            Carbon::parse($time)->toTimeString());
    }

    private function isUserHasFines(): void
    {
        $fines = UserFine::query()
            ->whereDate('day', $this->date)
            ->where('user_id', $this->user->id)
            ->get();

        $this->hasFineLessThanFiveMinutes = $fines->where('fine_id', Fine::TYPE_LATE_LESS_5)->count();
        $this->hasFineMoreThanFiveMinutes = $fines->where('fine_id', Fine::TYPE_LATE_MORE_5)->count();
    }

    private function getWorkDayShouldStartTime(): Carbon
    {
        return Carbon::createFromTimeString($this->user->workStartTime()->subHours($this->user->timezone - 1)->toTimeString());
    }
}