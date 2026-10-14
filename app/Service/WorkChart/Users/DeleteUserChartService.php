<?php
declare(strict_types=1);

namespace App\Service\WorkChart\Users;

use App\User;
use Exception;

/**
* Класс для работы с Service.
*/
class DeleteUserChartService
{
    /**
     * @throws Exception
     */
    public function handle(
        int $userId
    ): bool
    {
        $user = User::getUserById($userId);

        $updated = $user->update([
            'work_chart_id' => 0
        ]);

        if (!$updated)
        {
            throw new Exception("При удалений графика у сотрудника $user->full_name произошла ошибка");
        }

        return true;
    }
}