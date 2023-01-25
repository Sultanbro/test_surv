<?php

namespace App\Service\Analytics\Cells;

use App\DTO\Analytics\Cells\SaveCellActivityDTO;
use App\Enums\ErrorCode;
use App\Models\Analytics\UserStat;
use App\Repositories\Analytics\AnalyticColumnRepository;
use App\Repositories\Analytics\AnalyticStatRepository;
use App\Support\Core\CustomException;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
class CellActivityService
{
    public function __construct(
        private AnalyticColumnRepository $columnRepository,
        private AnalyticStatRepository $statRepository
    )
    {}

    public function handle(
        SaveCellActivityDTO $dto
    ): bool
    {
        $date = $this->getDate($dto->year, $dto->month);
        $columns = $this->columnRepository->getDays($date, $dto->groupId);

        foreach ($columns as $column)
        {
            $columnDate = $this->getDate($dto->year, $dto->month, $column->name);
            $stat = $this->statRepository->getStatisticOrFail($columnDate, $dto->rowId, $column->id);
            $totalForDay = UserStat::total_for_day($dto->activityId, $date);
            $totalForDay = floor($totalForDay * 10) / 10;

            if ($stat)
            {
                $stat->update([
                    'value'         => $totalForDay,
                    'show_value'    => $totalForDay,
                    'type'          => 'stat',
                    'class'         => $dto->class,
                    'activity_id'   => $dto->activityId
                ]);
            }
        }

        return true;
    }
    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return string
     */
    private function getDate(
        int $year,
        int $month,
        int $day = 1
    ): string
    {
        return Carbon::createFromDate($year,$month, $day)->format('Y-m-d');
    }
}