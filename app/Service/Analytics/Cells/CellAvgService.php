<?php

namespace App\Service\Analytics\Cells;

use App\DTO\Analytics\Cells\SaveCellActivityDTO;
use App\DTO\Analytics\Cells\SaveCellSumAvgDTO;
use App\Enums\ErrorCode;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use App\Repositories\Analytics\AnalyticColumnRepository;
use App\Repositories\Analytics\AnalyticStatRepository;
use App\Support\Core\CustomException;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
class CellAvgService extends CellService
{
    /**
     * @param SaveCellSumAvgDTO $dto
     * @return bool
     */
    public function handle(
        SaveCellSumAvgDTO $dto
    ): bool
    {
        $totalForDay = 0;
        $date = $this->getDate($dto->year, $dto->month);
        $stat = $this->statRepository->getStatisticOrNull($date, $dto->rowId, $dto->columnId);

        $totalForDay = AnalyticStat::daysAvg($date, $dto->rowId, $dto->groupId);

        $stat?->update([
            'value'      => $totalForDay,
            'show_value' => $totalForDay,
            'type'       => 'avg',
            'class'      => $dto->class,
        ]);

        return true;
    }
}