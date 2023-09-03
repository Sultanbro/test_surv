<?php

namespace App\Http\Controllers\V2\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\GetAnalyticsRequest;
use App\Http\Requests\V2\Analytics\GetFiredInfoRequest;
use App\ProfileGroup;
use App\Service\V2\Analytics\GetFiredInfoService;
use App\Service\V2\Analytics\GetGroupsService;
use App\Service\V2\Analytics\GetPerformanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class V2AnalyticController extends Controller
{
    /**
     * @param GetFiredInfoRequest $request
     * @param GetFiredInfoService $service
     * @return JsonResponse
     */
    public function firedInfo(GetFiredInfoRequest $request, GetFiredInfoService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data:  $service->handle($request->toDto())
        );
    }

    /**
     * @param GetGroupsService $service
     * @return JsonResponse
     */
    public function getGroups(GetGroupsService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle(),
            status: Response::HTTP_OK
        );
    }

    /**
     * @param GetAnalyticsRequest $request
     * @param GetPerformanceService $service
     * @return JsonResponse
     */
    public function getPerformances(GetAnalyticsRequest $request, GetPerformanceService $service): JsonResponse
    {
        $dto = $request->toDto();

        return $this->response(
            message: 'Success',
            data: $service->handle($dto),
            status: Response::HTTP_OK
        );
    }
    
    public function getAnalytics(): JsonResponse
    {
        //
    }
}
