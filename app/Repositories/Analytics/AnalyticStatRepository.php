<?php

namespace App\Repositories\Analytics;


use App\Enums\ErrorCode;
use App\Repositories\CoreRepository;
use App\Models\Analytics\AnalyticStat as Model;
use App\Support\Core\CustomException;

/**
* Класс для работы с Repository.
*/
class AnalyticStatRepository extends CoreRepository
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
     * Статистика за месяц по колонке и линий.
     *
     * @param string $firstDayOfMonth
     * @param int $rowId
     * @param int $columnId
     * @return object
     */
    public function getStatisticOrFail(
        string $firstDayOfMonth,
        int $rowId,
        int $columnId
    ): object
    {
        return $this->model()->where('date', $firstDayOfMonth)
            ->where('row_id', $rowId)
            ->where('column_id', $columnId);
    }
}