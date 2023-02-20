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
        try {
            $user = User::getUserById($dto->userId);
            $days = array_keys($dto->workdays);
            $workdays = [];

            foreach ($days as $day)
            {
                if (!$user->workdays()->where('workday_id', $day)->exists())
                {
                    $workdays[] = $day;
                }
            }

            $user->workdays()->attach($workdays);

            return true;

        }catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}