<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\KpiSaveRequest;
use App\Http\Requests\KpiUpdateRequest;
use App\Service\KpiService;
use Exception;
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

class KpiController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        View::share('title', 'KPI');
        View::share('menu', 'timetracking');

        return view('kpi')->with([
            'page' => 'kpi'
        ]);
    }

    public function getKpis(Request $request, KpiService $service)
    {
        $response = $service->get($request->input('id'));

        return response()->json($response);
    }

    /**
     * Сохранение.
     * @param KpiSaveRequest $request
     * @param KpiService $kpiService
     * @return JsonResponse
     * @throws Exception
     */
    public function save(KpiSaveRequest $request, KpiService $kpiService): JsonResponse
    {
        $response = $kpiService->save($request);

        return response()->json($response);
    }

    /**
     * Обновление.
     * @param KpiUpdateRequest $request
     * @param KpiService $kpiService
     * @return JsonResponse
     * @throws Exception
     */
    public function update(KpiUpdateRequest $request, KpiService $kpiService): JsonResponse
    {
        $response = $kpiService->update($request);

        return response()->json($response);
    }

    /**
     * Удаление.
     * @param Request $request
     * @param KpiService $kpiService
     * @return JsonResponse
     */
    public function delete(Request $request, KpiService $kpiService): JsonResponse
    {
        $response = $kpiService->delete($request);

        return response()->json($response);
    }
}
