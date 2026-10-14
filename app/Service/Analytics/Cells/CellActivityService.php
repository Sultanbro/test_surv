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
class CellActivityService extends CellService
{
    /**
     * @param SaveCellActivityDTO $dto
     * @return bool
     */
    public function handle(
        SaveCellActivityDTO $dto
    ): bool
    {
        $date = $this->getDate($dto->year, $dto->month);
        $columns = $this->columnRepository->getDays($date, $dto->groupId);

        foreach ($columns as $column)
        {
            $columnDate = $this->getDate($dto->year, $dto->month, $column->name);
            $stat = $this->statRepository->getStatisticOrNull($columnDate, $dto->rowId, $column->id);
            $totalForDay = UserStat::total_for_day($dto->activityId, $date);
            $totalForDay = floor($totalForDay * 10) / 10;

            $stat?->update([
                'value' => $totalForDay,
                'show_value' => $totalForDay,
                'type' => 'stat',
                'class' => $dto->class,
                'activity_id' => $dto->activityId
            ]);
        }

        return true;
    }
}