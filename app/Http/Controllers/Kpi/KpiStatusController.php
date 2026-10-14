<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\KpiStatusRequest;
use App\Models\Kpi\Kpi;
use Exception;
use Illuminate\Http\JsonResponse;

class KpiStatusController extends Controller
{

    /**
     * @param KpiStatusRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function setActive(KpiStatusRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        try {
            $status = Kpi::setActive($dto->kpiId, $dto->isActive);
        } catch (Exception $e) {
            $status = false;
        }
        return $this->response(
            message: 'Success',
            data: $status
        );
    }
}
