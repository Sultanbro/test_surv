<?php

namespace App\Http\Controllers\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkChart\AddUserChartRequest;
use App\Http\Requests\WorkChart\AttachUserWorkDaysRequest;
use App\Service\WorkChart\AddUserChartService;
use App\Service\WorkChart\AttachUserWorkDaysService;
use Exception;
use Illuminate\Http\JsonResponse;

class UserWorkChartController extends Controller
{
    /**
     * Выставляем график для пользователя.
     *
     * @param AddUserChartRequest $request
     * @param AddUserChartService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function addChart(AddUserChartRequest $request, AddUserChartService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Successfully added',
            data: $response
        );
    }

    /**
     * Выставляем дни работы для пользователя.
     *
     * @param AttachUserWorkDaysRequest $request
     * @param AttachUserWorkDaysService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function attachUserWorkDays(AttachUserWorkDaysRequest $request, AttachUserWorkDaysService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Successfully attached',
            data: $response
        );
    }
}
