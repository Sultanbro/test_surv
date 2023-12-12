<?php

namespace App\Http\Controllers\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkChart\Users\AddUserChartRequest;
use App\Http\Requests\WorkChart\Users\DeleteUserChartRequest;
use App\Jobs\ProcessAddUserChart;
use App\Service\WorkChart\Users\AddUserChartService;
use App\Service\WorkChart\Users\DeleteUserChartService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserWorkChartController extends Controller
{
    protected AddUserChartService $workChartService;
    public function __construct(AddUserChartService $service)
    {
        $this->workChartService = $service;
    }

    /**
     * Выставляем график для пользователя.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function addChart(Request $request): JsonResponse
    {

        $user = User::getUserById($request['user_id']);
        $user->work_chart_id = $request['work_chart_id'];
        $user->save();

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
