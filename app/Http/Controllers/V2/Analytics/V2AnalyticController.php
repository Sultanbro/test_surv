<?php

namespace App\Http\Controllers\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\GetAnalyticsRequest;
use App\Http\Requests\V2\Analytics\GetFiredInfoRequest;
use App\ProfileGroup;
use App\Service\V2\Analytics\GetActivitiesService;
use App\Service\V2\Analytics\GetAnalyticsService;
use App\Service\V2\Analytics\GetDecompositionsService;
use App\Service\V2\Analytics\GetFiredInfoService;
use App\Service\V2\Analytics\GetGroupsService;
use App\Service\V2\Analytics\GetPerformanceService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class V2AnalyticController extends Controller
{
    const SUCCESS_MESSAGE = 'Success loaded';

    const WARNING_MESSAGE= 'Something went wrong';


    /**
     * @param GetFiredInfoRequest $request
     * @param GetFiredInfoService $service
     * @return JsonResponse
     */
    public function firedInfo(GetFiredInfoRequest $request, GetFiredInfoService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data:  $service->handle($request->toDto()),
            status: Response::HTTP_OK
        );
    }

    /**
     * @param GetGroupsService $service
     * @return JsonResponse
     */
    public function getGroups(GetGroupsService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
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
            message: self::SUCCESS_MESSAGE,
            data: $service->handle($dto),
            status: Response::HTTP_OK
        );
    }

    /**
     * @param GetAnalyticsRequest $request
     * @param GetDecompositionsService $service
     * @return JsonResponse
     */
    public function getDecompositions(GetAnalyticsRequest $request, GetDecompositionsService $service): JsonResponse
    {
        $dto = $request->toDto();

        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->handle($dto),
            status: Response::HTTP_OK
        );
    }

    /**
     * @param GetAnalyticsRequest $request
     * @param GetAnalyticsService $service
     * @return JsonResponse
     */
    public function getAnalytics(GetAnalyticsRequest $request, GetAnalyticsService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->handle($request->toDto()),
            status: Response::HTTP_OK
        );
    }

    /**
     * @param GetAnalyticsRequest $request
     * @param GetActivitiesService $service
     * @return JsonResponse
     */
    public function getActivities(GetAnalyticsRequest $request, GetActivitiesService $service): JsonResponse
    {
        $dto = $request->toDto();

        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->handle($dto),
            status: Response::HTTP_OK
        );
    }
}
