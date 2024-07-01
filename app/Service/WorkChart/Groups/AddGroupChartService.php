<?php


namespace App\Service\WorkChart\Groups;

use App\DTO\WorkChart\Groups\AddGroupChartDTO;
use App\DTO\WorkChart\User\AddUserChartDTO;
use App\Models\GroupUser;
use App\ProfileGroup;
use App\User;
use Exception;

/**
* Класс для работы с Service.
*/
class AddGroupChartService
{
    /**
     * @throws Exception
     */
    public function handle(AddGroupChartDTO $dto): bool
    {
        $group = ProfileGroup::getById($dto->groupId);

        $updated = $group->update([
            'work_chart_id' => $dto->workChartId
        ]);

        if (!$updated)
        {
            throw new Exception("При обновлений графика у сотрудника $group->name произошла ошибка");
        }

        return true;
    }
}
