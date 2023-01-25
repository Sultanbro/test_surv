<?php

namespace App\Repositories\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Repositories\CoreRepository;
use App\Models\Analytics\AnalyticColumn as Model;

/**
* Класс для работы с Repository.
*/
class AnalyticColumnRepository extends CoreRepository
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
     * Получить день колонки от 1-31.
     *
     * @param string $firstDayOfMonth
     * @param int $groupId
     * @return object
     */
    public function getDays(
        string $firstDayOfMonth,
        int $groupId
    ): object
    {
        return $this->model()->where('date', $firstDayOfMonth)
            ->where('group_id', $groupId)
            ->whereNotIn('name', ['name','sum','avg', 'plan'])
            ->get();
    }
}