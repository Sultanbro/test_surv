<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\UpdatePortalRequest;
use App\Models\Portal\Portal;
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

        //TODO implement get saved kpiBacklight

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
        //TODO is owner guard
        $tenantId = tenant('id'); //TODO Portal refactor: portal associated with tenant for now
        $ownerId = auth()->id();

        $response = $service->handle($request->toDto($tenantId));
        return $this->response(
            message: 'Successfully updated',
            data: $response,
        );
    }
}
