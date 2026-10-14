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
    public function handle(AddUserChartDTO $dto): void
    {
        $user = User::getUserById($dto->userId);

        $user->update([
            'work_chart_id' => $dto->workChartId
        ]);
    }
}
