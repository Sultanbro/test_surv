<?php

namespace App\Repositories\WorkChart;

use App\Repositories\CoreRepository;
use App\Models\WorkChart\Workday as Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
* Класс для работы с Repository.
*/
class WorkdayRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param int $userId
     * @param string $date
     * @param int $weekNumber
     * @param int|null $day
     * @return Builder
     */
    public function getUserWorkDaysPerWeek(
        int $userId,
        string $date,
        int $weekNumber,
        ?int $day = null
    ): Builder
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;

        return $this->model()->where([
            ['user_id', '=', $userId],
            ['week_number', '=', $weekNumber],
        ])
            ->when($day, fn($record) => $record->where('day_of_week', $day))
            ->whereYear('date', $year)->whereMonth('date', $month);
    }
}