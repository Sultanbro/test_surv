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

class V2AnalyticGroupController extends Controller
{
    const SUCCESS_MESSAGE = 'Success loaded';

    const WARNING_MESSAGE= 'Something went wrong';

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
}