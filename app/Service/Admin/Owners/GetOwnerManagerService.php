<?php

namespace App\Service\Admin\Owners;

use App\DTO\Manager\GetOwnerDTO;
use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Models\CentralUser;
use App\Support\Core\CustomException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
* Класс для работы с Service.
*/
class GetOwnerManagerService
{
    public function handle(
    )
    {
        $owner = $this->getOwner();
        CentralUser::checkDomainExistOrFail($owner->id);

        return ManagerHasOwner::getManagerByOwnerIdOrFail($owner->id);
    }

    /**
     * @return Model
     */
    private function getOwner(): Model
    {
        $user = Auth::user();

        return CentralUser::getByEmail($user->email)->first();
    }
}