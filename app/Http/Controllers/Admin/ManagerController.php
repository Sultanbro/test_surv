<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Service\Admin\Managers\GetManagerService;
use Illuminate\Http\JsonResponse;

class ManagerController extends Controller
{
    /**
     * @param GetManagerService $service
     * @param int|null $managerId
     * @return JsonResponse
     */
    public function get(GetManagerService $service, int $managerId = null): JsonResponse
    {
        $handler = $service->handle($managerId);
        return $this->response(
            message: 'success',
            data:  $handler
        );
    }
}
