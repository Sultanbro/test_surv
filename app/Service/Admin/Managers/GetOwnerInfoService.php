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
     * @param GetOwnerInfoDTO $dto
     * @return object
     */
    public function handle(
        GetOwnerInfoDTO $dto
    ): object
    {
        $owner = CentralUser::getById($dto->ownerId)->first();

        if ($owner == null)
        {
            new CustomException("User with $dto->ownerId is not exist", ErrorCode::BAD_REQUEST, []);
        }

        return $owner;
    }
}