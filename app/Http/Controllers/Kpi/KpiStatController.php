<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\BonusesFilterRequest;
use App\Http\Requests\ShowKpiStatisticsRequest;
use App\Service\KpiStatisticService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\User;
use App\Models\Analytics\UpdatedUserStat;
use Illuminate\Http\Request;

class KpiStatController extends Controller
{
    /**
     * @var KpiStatisticService
     */
    public KpiStatisticService $service;

    public function __construct(KpiStatisticService $service)
    {
        $this->service = $service;
    }

    /**
     * Получаем:
     *
     * {
     *   method: 1,
     *   date: {
     *      'month': 2,
     *      'year': 2022
     *   }
     * }
     *
     * activity_id: id показателя.
     * kpi_id: какому kpi привязан показатель.
     * plan: сколько должны выполнить.
     * value: сколько выполнил.
     * percent: на сколько процентов покрывает.
     *
     * @param ShowKpiStatisticsRequest $request
     * @param User $id
     * @return JsonResponse
     * @throws Exception
     */
    public function show(ShowKpiStatisticsRequest $request, User $id): JsonResponse
    {
        $response = $this->service->get($request, $id);

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchGroups(Request $request): JsonResponse
    {
        $response = $this->service->fetch($request);

        return response()->json($response);
    }

    /**
     * Вытащить kpi по фильтрам
     */
    public function fetchKpis(Request $request): JsonResponse
    {
        $response = $this->service->fetchKpis($request);

        return response()->json($response);
    }

    /**
     * Данные с фронта:
     * {
     *      "targetable_type": 2,
     *      "targetable_id": 48,
     *      "month": 8,
     *      "year": 2022
     * }
     *
     * types: [
     *      1 => "App\User",
     *      2 => "App\ProfileGroup",
     *      3 => "App\Position"
     * ]
     *
     * Вытащить бонусы по фильтрам.
     * @param BonusesFilterRequest $request
     * @return JsonResponse
     */
    public function fetchBonuses(BonusesFilterRequest $request): JsonResponse
    {
        $response = $this->service->fetchBonuses($request);

        return response()->json($response);
    }

    /**
     * Вытащить премии по фильтрам
     */
    public function fetchQuartalPremiums(Request $request): JsonResponse
    {
        $response = $this->service->fetchQuartalPremiums($request);

        return response()->json($response);
    }

    /**
     * API
     * @param Request $request
     * @return JsonResponse
     */
    public function workdays(Request $request): JsonResponse
    {
        $response = $this->service->userWorkdays($request);

        return response()->json($response);
    }

    /**
     * @param Request $request
     */
    public function updateStat(Request $request)
    {
        UpdatedUserStat::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'activity_id' => $request->activity_id,
            'kpi_item_id' => $request->kpi_item_id,
            'value' => $request->value,
        ]);
    }
}
