<?php

namespace App\Http\Controllers\Kpi;

use App\Exceptions\Kpi\TargetDuplicateException;
use App\Http\Controllers\Controller;
use App\Http\Requests\KpiSaveRequest;
use App\Http\Requests\KpiUpdateRequest;
use App\Service\KpiService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class KpiController extends Controller
{
    protected KpiService $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    public function index(): Factory|\Illuminate\Contracts\View\View|Application
    {
        View::share('title', 'KPI');
        View::share('menu', 'timetracking');

        return view('kpi')->with([
            'page' => 'kpi'
        ]);
    }

    public function getKpis(Request $request): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $this->kpiService->fetch($request->get('filters')),
            status: Response::HTTP_OK
        );
    }

    /**
     * @throws Throwable
     * @throws TargetDuplicateException
     */
    public function save(KpiSaveRequest $request): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $this->kpiService->save($request),
            status: Response::HTTP_CREATED
        );
    }

    /**
     * @throws Throwable
     */
    public function update(KpiUpdateRequest $request): JsonResponse
    {
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $this->kpiService->update($request),
            status: Response::HTTP_ACCEPTED
        );
    }

    public function delete(Request $request, int $id): JsonResponse
    {
        $this->kpiService->delete($request);
        return $this->response(
            message: self::SUCCESS_MESSAGE,
            data: $id,
            status: Response::HTTP_NO_CONTENT
        );
    }
}
