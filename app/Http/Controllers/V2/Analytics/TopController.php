<?php

namespace App\Http\Controllers\V2\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\GetRentabilityRequest;
use App\Service\V2\Analytics\GetRentabilityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
