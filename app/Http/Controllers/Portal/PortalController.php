<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\UpdatePortalRequest;
use App\Models\Portal\Portal;
use App\Models\Role;
use App\Service\Portal\UpdatePortalService;
use Illuminate\Http\JsonResponse;
use Exception;

class PortalController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getCurrentPortal(): JsonResponse
    {
        $tenantId = tenant('id'); //TODO Portal refactor: portal associated with tenant for now

        return $this->response(
            message: "Success",
            data: Portal::getByTenantIdOrFail($tenantId),
        );
    }

    /**
     * Обновление портала.
     *
     * @param UpdatePortalRequest $request
     * @param UpdatePortalService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function update(UpdatePortalRequest $request, UpdatePortalService $service): JsonResponse
    {
        $tenantId = tenant('id');
        $userPermissions = auth()->user()->getAllPermissions()->pluck('name')->toArray();

        $role = Role::where('id', auth()->user()->role_id)->first();
        $rolePermissions = $role ? $role->permissions->pluck('name')->toArray() : [];

        if(
            !in_array('kpi_edit', $rolePermissions)
            && !in_array('kpi_edit', $userPermissions)
            && auth()->user()->is_admin != 1
        ){
            throw new Exception("Нет доступа.");
        }

        $response = $service->handle($request->toDto($tenantId));
        return $this->response(
            message: 'Successfully updated',
            data: $response,
        );
    }
}
