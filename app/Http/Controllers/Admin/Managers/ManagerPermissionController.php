<?php

namespace App\Http\Controllers\Admin\Managers;

use App\Enums\ErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Managers\GetOwnerInfoRequest;
use App\Models\CentralUser;
use App\Service\Admin\Managers\GetOwnerInfoService;
use App\User;
use Illuminate\Http\JsonResponse;

class ManagerPermissionController extends Controller
{
    /**
     * @param GetOwnerInfoRequest $request
     * @param GetOwnerInfoService $service
     * @return JsonResponse
     */
    public function getOwnerInfo(GetOwnerInfoRequest $request, GetOwnerInfoService $service): JsonResponse
    {
        $manager = auth()->user();
        /**@var CentralUser $owner */
        $owner = tenant()->users()->first();

        abort_if(!$manager->can('owner_view'), ErrorCode::FORBIDDEN, 'У вас нет доступа');

        return $this->response(
            message: 'Success',
            data: $service->handle($owner->id)
        );
    }
}
