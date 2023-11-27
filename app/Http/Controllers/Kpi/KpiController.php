<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\KpiSaveRequest;
use App\Http\Requests\KpiUpdateRequest;
use App\Service\KpiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

// use App\Models\Kpi\Kpi;

class KpiController extends Controller
{
    protected KpiService $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
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

    public function getKpis(Request $request): JsonResponse
    {
        $response = $this->kpiService->fetch($request->filters);

        return response()->json($response);
    }

    public function save(KpiSaveRequest $request): JsonResponse
    {
        $response = $this->kpiService->save($request);

        return response()->json($response);
    }

    public function update(KpiUpdateRequest $request): JsonResponse
    {

        $response = $this->kpiService->update($request);

        return response()->json($response);
    }

    public function delete(Request $request, int $id): JsonResponse
    {
        $this->kpiService->delete($request);

        return response()->json($id);
    }
}
