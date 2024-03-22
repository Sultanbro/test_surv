<?php

namespace App\Service\Admin\Owners;

use App\DTO\Manager\GetOwnerDTO;
use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Models\CentralUser;
use App\Support\Core\CustomException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

/**
* Класс для работы с Service.
*/
class GetOwnerManagerService
{
    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Exception
     */
    public function handle()
    {
        /** @var CentralUser $owner */
        $owner = $this->getOwner();
        CentralUser::checkDomainExistOrFail($owner->id);

        tenancy()->initialize(tenant: 'admin');
        $model = ManagerHasOwner::query()->withWhereHas('manager')->where('owner_id', $owner->id)->first();

        return $model?->manager;
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
