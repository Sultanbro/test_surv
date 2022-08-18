<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Models\Analytics\Activity;
use App\Service\IndicatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Service\ActivityService;
use App\Http\Requests\ActivitySaveRequest;
use App\Http\Requests\ActivityUpdateRequest;

class IndicatorController extends Controller
{
    protected $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
        $this->middleware('superuser');
    }
    /**
     * Получаем все показатели из таблицы indicators.
     */
    public function getAllIndicators(): JsonResponse
    {
        $indicators = Activity::query()->get()->makeHidden([
            'unit',
            'order',
            'editable',
            'data'
        ]);

        return response()->json($indicators);
    }

    /**
     * Страница для Показателя
     * @param $id
     */
    public function showIndicator($id): JsonResponse
    {
        $indicator = Activity::withTrashed()->findOrFail($id);

        return response()->json($indicator);
    }


    /**
     * Сохранение
     */
    public function save(ActivitySaveRequest $request): JsonResponse
    {
        $response = $this->activityService->save($request);

        return response()->json($response);
    }

    /**
     * Обновление
     */
    public function update(ActivityUpdateRequest $request): JsonResponse
    {
        $response = $this->activityService->update($request);

        return response()->json($response);
    }

    /**
     * Удаление
     */
    public function delete(Request $request): JsonResponse
    {
        $response = $this->activityService->delete($request);

        return response()->json($response);
    }
}
