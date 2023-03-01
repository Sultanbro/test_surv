<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service\Admin\Owners\GetOwnerManagerService;
use App\Service\Admin\Owners\OwnerInfoService;
use Illuminate\Http\JsonResponse;

class OwnerController extends Controller
{
    /**
     * @param GetOwnerManagerService $service
     * @return JsonResponse
     */
    public function getManager(GetOwnerManagerService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle()
        );
    }

    /**
     * Инфо о текущего клиента.
     *
     * @param OwnerInfoService $service
     * @return JsonResponse
     */
    public function info(OwnerInfoService $service): JsonResponse
    {
        return $this->response(
            message: "Success",
            data: $service->handle()
        );
    }
}
