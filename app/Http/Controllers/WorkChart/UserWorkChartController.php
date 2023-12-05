<?php

namespace App\Http\Controllers\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkChart\Users\AddUserChartRequest;
use App\Http\Requests\WorkChart\Users\DeleteUserChartRequest;
use App\Service\WorkChart\Users\AddUserChartService;
use App\Service\WorkChart\Users\DeleteUserChartService;
use Exception;
use Illuminate\Http\JsonResponse;

class UserWorkChartController extends Controller
{
    /**
     * Выставляем график для пользователя.
     *
     * @param AddUserChartRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addChart(AddUserChartRequest $request): JsonResponse
    {
        /** @var AddUserChartService $service */
        $service = app(AddUserChartService::class);
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
}
