<?php

namespace App\Http\Controllers\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkChart\Users\AddUserChartRequest;
use App\Http\Requests\WorkChart\Users\DeleteUserChartRequest;
use App\Jobs\ProcessAddUserChart;
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
        ProcessAddUserChart::dispatch($request->toDto());
        return $this->response(
            message: 'Successfully added'
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
