<?php

namespace App\Http\Controllers\Admin\Owners;

use App\Http\Controllers\Controller;
use App\Service\Admin\Owners\GetOwnerManagerService;
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
}
