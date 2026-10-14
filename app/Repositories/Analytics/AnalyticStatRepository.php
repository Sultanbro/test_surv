<?php

namespace App\Repositories\Analytics;


use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\AnalyticStat as Model;
use App\Repositories\CoreRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Класс для работы с Repository.
 */
class AnalyticStatRepository extends CoreRepository
{
    /**
     * @return Collection<AnalyticStat>
     */
    public function getByGroupId(int $groupId, string|Carbon $date): Collection
    {
        $date = Carbon::parse($date);

        return AnalyticStat::with('activity')
            ->where('date', $date)
            ->where('group_id', $groupId)
            ->whereRelation('column', fn($query) => $query->whereNot('name', 'plan'))
            ->get();
    }

    public function getByGroupIds(array $groupIds, string|Carbon $date): Collection
    {
        $date = Carbon::parse($date);

        return AnalyticStat::with('activity')
            ->where('date', $date)
            ->whereIn('group_id', $groupIds)
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