<?php

namespace App\Repositories;

use App\Timetracking as Model;
use Carbon\Carbon;

/**
* Класс для работы с Repository.
*/
class TimeTrackingRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * @param int $userId
     * @param int $year
     * @param int $month
     * @param int $day
     */
    public function getTrackingTimeForUser(
        int $userId,
        int $year,
        int $month,
        int $day
    )
    {
        return $this->model()->where('user_id', $userId)
            ->whereYear('enter', $year)
            ->whereMonth('enter', $month)
            ->whereDay('enter', $day)
            ->first();
    }

    public function updateOrCreate(
        int $userId,
        int $year,
        int $month,
        int $day,
        string $time,
        string $enter,
        ?string $comment
    )
    {
        $timeTrack = $this->getTrackingTimeForUser($userId, $year, $month, $day);
        $description = '';

        if ($timeTrack) {
            $description = "Изменено: $time $comment";
            $timeTrack->update([
                'enter' => $enter,
                'user_id' => $userId,
                'updated' => 1
            ]);
        } else {
            $description = "Добавлено: $time $comment";
            $this->model()->create([
                'enter' => $enter,
                'user_id' => $userId,
                'updated' => 1
            ]);
        }

        return $description;
    }
}