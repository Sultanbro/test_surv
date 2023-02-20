<?php

namespace App\Http\Controllers\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkChart\AddUserChartRequest;
use App\Http\Requests\WorkChart\AttachUserWorkDaysRequest;
use App\Http\Requests\WorkChart\DeleteUserChartRequest;
use App\Service\WorkChart\AddUserChartService;
use App\Service\WorkChart\AttachUserWorkDaysService;
use App\Service\WorkChart\DeleteUserChartService;
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
     * Сброс график работы.
     *
     * @param DeleteUserChartRequest $request
     * @param DeleteUserChartService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteChart(DeleteUserChartRequest $request, DeleteUserChartService $service): JsonResponse
    {
        $response = $service->handle($request->toDto()->userId);

        return $this->response(
            message: 'Successfully deleted',
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
