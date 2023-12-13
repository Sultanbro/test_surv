<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\BonusesFilterRequest;
use App\Http\Requests\Kpi\Statistics\UserGroupsRequest;
use App\Http\Requests\ShowKpiStatisticsRequest;
use App\Http\Requests\UpdatedUserStatUpdateRequest;
use App\Models\Analytics\Activity;
use App\Service\Kpi\Statistic\UserGroupService;
use App\Service\KpiStatisticService;
use App\Service\UpdatedUserStatService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

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
     * Вытащить список Групп и Пользователей для KPI
     */
    public function fetchKpiGroupsAndUsers(Request $request): JsonResponse
    {
        $response = $this->service->fetchKpiGroupsAndUsers($request);

        return response()->json($response);
    }

    /**
     * Вытащить список Пользователей с показателями KPI для указанной targetable_id
     */
    public function showKpiGroupAndUsers(Request $request, $targetableId): JsonResponse
    {
        $response = $this->service->fetchKpiGroupOrUser($request, $targetableId);

        return response()->json($response);
    }

    /**
     * Возвращает процент выполнения KPI по месяцам года
     */
    public function getAnnualStatistics(Request $request): JsonResponse
    {
        $response = $this->service->fetchAnnualKPIPercent($request);

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
     * @return JsonResponse
     */
    public function readKpis(Request $request): JsonResponse
    {
        $this->service->readKpis(Auth::id());

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function readQuartalPremiums(Request $request): JsonResponse
    {
        $this->service->readQuartalPremiums(Auth::id());

        return response()->json([
            'status' => 'success',
        ]);
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
     * @param UpdatedUserStatUpdateRequest $request
     * @return mixed
     */
    public function updateStat(UpdatedUserStatUpdateRequest $request)
    {
        $response = (new UpdatedUserStatService)->updateOrCreate(
            $request->user_id ?? null,
            $request->activity_id ?? null,
            $request->kpi_item_id ?? null,
            $request->date ?? null,
            $request->value ?? null
        );

        Artisan::call('user:save_kpi', [
            'date' => $request->date,
            'user_id' => $request->user_id
        ]);

        return response()->success($response);
    }

    /**
     * get all activites
     *
     * @return Array
     */
    public function getActivities()
    {
        return Activity::get()->keyBy('id');
    }

    /**
     * @param UserGroupsRequest $request
     * @param UserGroupService $service
     * @return JsonResponse
     */
    public function groups(UserGroupsRequest $request, UserGroupService $service): JsonResponse
    {
        $dto = $request->toDto();

        return $this->response(
            message: 'Success',
            data: $service->handle($dto)
        );
    }
}
