<?php

namespace App\Repositories;

use App\Timetracking as Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->model()
            ->where('user_id', $userId)
            ->whereYear('enter', $year)
            ->whereMonth('enter', $month)
            ->whereDay('enter', $day)
            ->first();
    }

    public function updateOrCreate(
        int     $userId,
        int     $year,
        int     $month,
        int     $day,
        string  $time,
        string  $enter,
        ?string $comment
    ): string
    {
        $timeTrack = $this->getTrackingTimeForUser($userId, $year, $month, $day);

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
                'updated' => 0
            ]);
        }

        return $description;
    }

    /**
     * @param ?int $userId
     * @return Builder
     */
    public function getNonUpdatedTimeTrackWithUser(
        ?int $userId = null,
    ): Builder
    {
        return $this->model()
            ->with('user')
            ->select('id', 'enter', 'exit', 'total_hours', 'user_id')
            ->when($userId, fn($query) => $query->where('user_id', $userId))
            ->where('updated', 0)
            ->where('total_hours', 0)
            ->whereNotNull('exit');
    }
}