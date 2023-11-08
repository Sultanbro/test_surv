<?php

namespace App\Repositories\Analytics;


use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\AnalyticStat as Model;
use App\Repositories\CoreRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Класс для работы с Repository.
 */
class AnalyticStatRepository extends CoreRepository
{
    public function getByGroupId(int $groupId, string $date): Collection|array
    {
        return AnalyticStat::with('activity')
            ->where('date', $date)
            ->where('group_id', $groupId)
            ->get();
    }

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
     * @return object|null
     */
    public function getStatisticOrNull(
        string $firstDayOfMonth,
        int    $rowId,
        int    $columnId
    ): object|null
    {
        return $this->model()->where('date', $firstDayOfMonth)
            ->where('row_id', $rowId)
            ->where('column_id', $columnId)->first();
    }
}