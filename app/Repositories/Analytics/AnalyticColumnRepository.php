<?php

namespace App\Repositories\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticColumn as Model;
use App\Repositories\CoreRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Класс для работы с Repository.
 */
class AnalyticColumnRepository extends CoreRepository
{
    /**
     * @return Collection<AnalyticColumn>
     */
    public function getByGroupId(int $groupId, string $date): Collection
    {
        return AnalyticColumn::query()
            ->where('date', $date)
            ->where('group_id', $groupId)
            ->orderBy('order')
            ->get();
    }

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
     * Получить день колонки от 1-31.
     *
     * @param string $firstDayOfMonth
     * @param int $groupId
     * @return object
     */
    public function getDays(
        string $firstDayOfMonth,
        int    $groupId
    ): object
    {
        return $this->model()->where('date', $firstDayOfMonth)
            ->where('group_id', $groupId)
            ->whereNotIn('name', ['name', 'sum', 'avg', 'plan'])
            ->get();
    }
}