<?php

namespace App\Service\Admin\Managers;

use App\DTO\Manager\GetOwnerDTO;
use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Models\CentralUser;
use App\Models\Role;
use App\Support\Core\CustomException;

/**
* Класс для работы с Service.
*/
class GetManagerService
{
    /**
     * @param int|null $managerId
     * @return ?object
     */
    public function handle(
        ?int $managerId
    ): ?object
    {
        return Role::getByNameOrFail('Manager для работы с клиентами JobTron')
            ->users()
            ->when($managerId, fn ($query) => $query->where('id', $managerId))
            ->get();
    }
}