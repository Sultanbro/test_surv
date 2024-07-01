<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Managers\GetOwnerRequest;
use App\Http\Requests\Admin\Managers\PutManagerToOwnerRequest;
use App\Service\Admin\Managers\GetOwnerService;
use App\Service\Admin\Managers\PutManagerToOwnerService;
use Illuminate\Http\JsonResponse;

class ManagerOwnerController extends Controller
{
    /**
     * @param PutManagerToOwnerRequest $request
     * @param PutManagerToOwnerService $service
     * @return JsonResponse
     */
    public function putManagerToOwner(PutManagerToOwnerRequest $request, PutManagerToOwnerService $service): JsonResponse
    {
        return $this->response(
            message: 'success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param GetOwnerRequest $request
     * @param GetOwnerService $service
     * @return JsonResponse
     */
    public function getOwner(GetOwnerRequest $request, GetOwnerService $service): JsonResponse
    {
        return $this->response(
            message: 'success',
            data: $service->handle($request->toDto()->managerId)
        );
    }
}
