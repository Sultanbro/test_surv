<?php
declare(strict_types=1);

namespace App\Service\WorkChart;

use App\DTO\WorkChart\AttachUserWorkDaysDTO;
use App\User;
use Exception;

/**
* Класс для работы с Service.
*/
class AttachUserWorkDaysService
{
    /**
     * @param AttachUserWorkDaysDTO $dto
     * @return bool
     * @throws Exception
     */
    public function handle(AttachUserWorkDaysDTO $dto): bool
    {
        $user = User::getUserById($dto->userId);
        $workdays = array_keys($dto->workdays);

        if (!$user->workdays()->attach($workdays))
        {
            throw new Exception("При выставлений рабочих дней для пользователя $user->full_name произошла ошибка");
        }

        return true;
    }
}