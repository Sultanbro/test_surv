<?php

namespace App\Http\Controllers\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkChart\StoreWorkChartRequest;
use App\Http\Requests\WorkChart\UpdateWorkChartRequest;
use App\Http\Resources\WorkChart\WorkChartResource;
use App\Models\WorkChart\WorkChartModel;
use App\Service\WorkChart\AddWorkChartService;
use App\Service\WorkChart\UpdateWorkChartService;
use Exception;
use Illuminate\Http\JsonResponse;


class WorkChartController extends Controller
{
    /**
     * Все графики работы.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: WorkChartResource::collection(WorkChartModel::with('workChartType')->get())
        );
    }

    /**
     * Создать график работы.
     *
     * @param StoreWorkChartRequest $request
     * @param AddWorkChartService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreWorkChartRequest $request, AddWorkChartService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());
        return $this->response(
            'Successfully added',
            $response
        );
    }

    /**
     * Получить график работы по id.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $workChart = WorkChartModel::query()->findOrFail($id);
        return $this->response(
            message: 'Success',
            data: $workChart
        );
    }

    /**
     * Обновление графика.
     *
     * @param UpdateWorkChartRequest $request
     * @param UpdateWorkChartService $service
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(UpdateWorkChartRequest $request, UpdateWorkChartService $service, int $id): JsonResponse
    {
        $response = $service->handle($request->toDto($id));
        return $this->response(
            message: 'Successfully updated',
            data: $response
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->response(
            message: 'Successfully deleted',
            data: WorkChartModel::deleteByOne($id)
        );
    }
}
