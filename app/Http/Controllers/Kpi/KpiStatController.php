<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowKpiStatisticsRequest;
use App\Service\KpiStatisticService;
use Exception;
use Illuminate\Http\JsonResponse;
use App\User;
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
    
}
