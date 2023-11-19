<?php

namespace App\Http\Controllers\V2\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\AddRowAnalyticsRequest;
use App\Http\Requests\V2\Analytics\CreateAnalyticsRequest;
use App\Http\Requests\V2\Analytics\GetAnalyticsRequest;
use App\Service\V2\Analytics\AddRowAnalyticsService;
use App\Service\V2\Analytics\CreateAnalyticsService;
use Illuminate\Http\JsonResponse;
use Throwable;


class V2AnalyticController extends Controller
{
    const SUCCESS_MESSAGE = 'Success loaded';

    const WARNING_MESSAGE= 'Something went wrong';

    /**
     * @param CreateAnalyticsRequest $request
     * @param CreateAnalyticsService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function create(CreateAnalyticsRequest $request, CreateAnalyticsService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param AddRowAnalyticsRequest $request
     * @param AddRowAnalyticsService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function addRow(AddRowAnalyticsRequest $request, AddRowAnalyticsService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->handle($request->toDto())
        );
    }
}
