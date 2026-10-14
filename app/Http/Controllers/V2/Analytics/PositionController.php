<?php

namespace App\Http\Controllers\V2\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\GetAnalyticPositionsRequest;
use App\Service\V2\Analytics\GetPositionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * @param GetAnalyticPositionsRequest $request
     * @param GetPositionService $service
     * @return JsonResponse
     */
    public function get(GetAnalyticPositionsRequest $request, GetPositionService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }
}
