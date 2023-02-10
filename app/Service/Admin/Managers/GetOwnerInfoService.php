<?php

namespace App\Service\Admin\Managers;

use App\DTO\Manager\GetOwnerInfoDTO;
use App\Enums\ErrorCode;
use App\Models\CentralUser;
use App\Support\Core\CustomException;

/**
* Класс для работы с Service.
*/
class GetOwnerInfoService
{
    /**
     * @param int $ownerId
     * @return object
     */
    public function handle(
        int $ownerId
    ): object
    {
        $owner = CentralUser::getById($ownerId)->first();

        if ($owner == null)
        {
            new CustomException("User with $ownerId is not exist", ErrorCode::BAD_REQUEST, []);
        }

        return $owner;
    }
}