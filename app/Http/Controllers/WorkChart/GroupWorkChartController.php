<?php

namespace App\Http\Controllers\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkChart\Groups\AddGroupChartRequest;
use App\Http\Requests\WorkChart\Groups\DeleteGroupChartRequest;
use App\Service\WorkChart\Groups\AddGroupChartService;
use App\Service\WorkChart\Groups\DeleteGroupChartService;
use Exception;
use Illuminate\Http\JsonResponse;

class GroupWorkChartController extends Controller
{
    /**
     * Выставляем график для группы.
     *
     * @param AddGroupChartRequest $request
     * @param AddGroupChartService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function addChart(AddGroupChartRequest $request, AddGroupChartService $service): JsonResponse
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
     * @param DeleteGroupChartRequest $request
     * @param DeleteGroupChartService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteChart(DeleteGroupChartRequest $request, DeleteGroupChartService $service): JsonResponse
    {
        $response = $service->handle($request->toDto()->groupId);

        return $this->response(
            message: 'Successfully deleted',
            data: $response
        );
    }
}
