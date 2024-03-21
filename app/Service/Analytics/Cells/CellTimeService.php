<?php

namespace App\Service\Analytics\Cells;

use App\DTO\Analytics\Cells\SaveCellTimeDTO;
use App\Repositories\Analytics\AnalyticColumnRepository;
use App\Repositories\Analytics\AnalyticStatRepository;
use App\Timetracking;

/**
* Класс для работы с Service.
*/
class CellTimeService extends CellService
{
    /**
     * @param SaveCellTimeDTO $dto
     * @return bool
     */
    public function handle(
        SaveCellTimeDTO $dto
    ): bool
    {
        $date = $this->getDate($dto->year, $dto->month);
        $columns = $this->columnRepository->getDays($date, $dto->groupId);

        foreach ($columns as $column)
        {
            $columnDate = $this->getDate($dto->year, $dto->month, $column->name);
            $stat = $this->statRepository->getStatisticOrNull($columnDate, $dto->rowId, $column->id);
            $totalForDay = Timetracking::totalHours($date, $dto->groupId);
            $totalForDay = floor($totalForDay / 9 * 10 / 10);

            $stat?->update([
                'value' => $totalForDay,
                'show_value' => $totalForDay,
                'type' => 'time',
                'class' => $dto->class,
            ]);
        }

        return true;
    }
}