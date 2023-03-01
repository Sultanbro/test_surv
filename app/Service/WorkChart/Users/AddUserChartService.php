<?php
declare(strict_types=1);

namespace App\Service\WorkChart\Users;

use App\DTO\WorkChart\User\AddUserChartDTO;
use App\User;
use Exception;

/**
* Класс для работы с Service.
*/
class AddUserChartService
{
    /**
     * @throws Exception
     */
    public function handle(AddUserChartDTO $dto): bool
    {
        $user = User::getUserById($dto->userId);

        $updated = $user->update([
            'work_chart_id' => $dto->workChartId
        ]);

        if (!$updated)
        {
            throw new Exception("При обновлений графика у сотрудника $user->full_name произошла ошибка");
        }

        return $updated;
    }
}