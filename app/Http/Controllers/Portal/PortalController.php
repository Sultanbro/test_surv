<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Portal\Portal;
use Illuminate\Http\JsonResponse;

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
}
