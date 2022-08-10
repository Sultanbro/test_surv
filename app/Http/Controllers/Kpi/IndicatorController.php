<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Models\Analytics\Activity;
use App\Service\IndicatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{
    public function __construct()
    {
        $this->middleware('superuser');
    }
    /**
     * Получаем все показатели из таблицы indicators.
     *
     * @return JsonResponse
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
     * @return JsonResponse
     */
    public function showIndicator($id): JsonResponse
    {
        $indicator = Activity::withTrashed()->findOrFail($id);

        return response()->json($indicator);
    }
}
