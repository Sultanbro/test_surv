<?php

namespace App\Service\Admin\Owners;

use App\DTO\Manager\GetOwnerDTO;
use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Models\CentralUser;
use App\Support\Core\CustomException;

/**
* Класс для работы с Service.
*/
class GetOwnerManagerService
{
    public function handle(
    )
    {
        $ownerId = auth()->id() ?? 1;
        CentralUser::checkDomainExistOrFail($ownerId);

        return ManagerHasOwner::getManagerByOwnerIdOrFail($ownerId);
    }
}