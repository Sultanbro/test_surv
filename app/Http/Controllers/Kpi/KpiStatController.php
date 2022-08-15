<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Service\KpiStatisticService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\Position;
use App\Models\Analytics\Activity;
// use App\Models\Kpi\Kpi;

class KpiStatController extends Controller
{
    /**
     * Получаем:
     * activity_id: id показателя.
     * kpi_id: какому kpi привязан показатель.
     * plan: сколько должны выполнить.
     * value: сколько выполнил.
     * percent: на сколько процентов покрывает.
     *
     * @param KpiStatisticService $kpiStatisticService
     * @param User $id
     * @return JsonResponse
     */
    public function show(KpiStatisticService $kpiStatisticService, User $id): JsonResponse
    {
        $response = $kpiStatisticService->get($id);

        return response()->json($response);
    }

}
