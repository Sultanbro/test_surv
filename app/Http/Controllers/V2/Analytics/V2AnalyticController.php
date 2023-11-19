<?php

namespace App\Http\Controllers\V2\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\CreateAnalyticsRequest;
use App\Http\Requests\V2\Analytics\GetAnalyticsRequest;
use App\Service\V2\Analytics\CreateAnalyticsService;
use Illuminate\Http\JsonResponse;


class V2AnalyticController extends Controller
{
    const SUCCESS_MESSAGE = 'Success loaded';

    const WARNING_MESSAGE= 'Something went wrong';

    /**
     * @param CreateAnalyticsRequest $request
     * @param CreateAnalyticsService $service
     * @return JsonResponse
     */
    public function create(CreateAnalyticsRequest $request, CreateAnalyticsService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->handle($request->toDto())
        );
    }
}
