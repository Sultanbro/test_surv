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
    public function handle(PutManagerToOwnerDTO $dto): bool
    {
        return ManagerHasOwner::createRecord($dto->ownerId, $dto->managerId);
    }
}
