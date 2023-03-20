<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kpi\KpiStatusRequest;
use App\Models\Kpi\Kpi;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return $this->response(
            message: 'Success',
            data: Kpi::setActive($dto->kpiId, $dto->isActive)
        );
    }
}
