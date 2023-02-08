<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Managers\GetOwnerInfoRequest;
use App\Http\Requests\Admin\Managers\GetOwnerRequest;
use App\Http\Requests\Admin\Managers\PutManagerToOwnerRequest;
use App\Service\Admin\Managers\GetOwnerInfoService;
use App\Service\Admin\Managers\GetOwnerService;
use App\Service\Admin\Managers\PutManagerToOwnerService;
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
        $manager = auth()->user() ?? User::find(13865);

        abort_if(!$manager->can('owner_view'), ErrorCode::FORBIDDEN, 'У вас нет доступа');

        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto()->ownerId)
        );
    }
}
