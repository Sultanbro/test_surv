<?php

namespace App\Service\Admin\Managers;

use App\DTO\Manager\PutManagerToOwnerDTO;
use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Support\Core\CustomException;

/**
* Класс для работы с Service.
*/
class PutManagerToOwnerService
{
    public function handle(
        PutManagerToOwnerDTO $dto
    ): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
    {
        $success = ManagerHasOwner::createRecord($dto->ownerId, $dto->managerId);
        if (!$success)
        {
            new CustomException('Ошибка при добавлений данных в manager_has_owner', ErrorCode::BAD_REQUEST, []);
        }

        return $success;
    }
}