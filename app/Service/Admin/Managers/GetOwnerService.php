<?php

namespace App\Service\Admin\Managers;

use App\DTO\Manager\GetOwnerDTO;
use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Models\CentralUser;
use App\Support\Core\CustomException;

/**
* Класс для работы с Service.
*/
class GetOwnerService
{
    public function handle(
        int $managerId
    )
    {
        $ownerIds = ManagerHasOwner::getOwnerByManagerIdToArray($managerId);
        if (count($ownerIds) <= 0)
        {
            new CustomException('У менеджера нет клиента который он(-a) привязан(-a)', ErrorCode::BAD_REQUEST, []);
        }

        return CentralUser::getUsersById($ownerIds);
    }
}