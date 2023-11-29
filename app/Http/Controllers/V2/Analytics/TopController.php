<?php

namespace App\Http\Controllers\V2\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\GetRentabilityRequest;
use App\Http\Requests\V2\Analytics\SpeedometersRequest;
use App\Service\V2\Analytics\GetPredictsService;
use App\Service\V2\Analytics\GetRentabilityService;
use App\Service\V2\Analytics\RentabilitySpeedometerService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class TopController extends Controller
{
    const SUCCESS_MESSAGE = 'Success';

    /**
     * @param GetRentabilityRequest $request
     * @param GetRentabilityService $rentabilityService
     * @return JsonResponse
     * @throws Exception
     */
    public function getRentability(GetRentabilityRequest $request, GetRentabilityService $rentabilityService): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $rentabilityService->handle($request->toDto())
        );
    }

    /**
     * @param SpeedometersRequest $request
     * @param RentabilitySpeedometerService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function rentabilitySpeedometers(SpeedometersRequest $request, RentabilitySpeedometerService $service): JsonResponse
    {
        return $this->response(
          message: self::SUCCESS_MESSAGE,
          data: $service->handle($request->toDto())
        );
    }

    /**
     * @param GetPredictsService $service
     * @return JsonResponse
     */
    public function getPredicts(GetPredictsService $service): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $service->handle()
        );
    }
}
