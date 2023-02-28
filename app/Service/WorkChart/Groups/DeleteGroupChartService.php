<?php
declare(strict_types=1);

namespace App\Service\WorkChart\Groups;

use App\Enums\WorkChart\WorkChartEnum;
use App\Models\WorkChart\WorkChartModel;
use App\ProfileGroup;
use App\User;
use Exception;

/**
* Класс для работы с Service.
*/
class DeleteGroupChartService
{
    /**
     * @throws Exception
     */
    public function handle(
        int $groupId
    ): bool
    {
        $group = ProfileGroup::getById($groupId);
        $chart = WorkChartModel::getByNameOrFail(WorkChartEnum::SIX_BY_ONE);

        /**
         * Если удаляется график работы для группы по умолчанию для группы график работы 6-1.
         */
        $updated = $group->update([
            'work_chart_id' => $chart->id
        ]);

        if (!$updated)
        {
            throw new Exception("При удалений графика у группы $group->name произошла ошибка");
        }

        return true;
    }
}