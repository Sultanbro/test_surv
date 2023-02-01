<?php

namespace App\Http\Controllers\Settings\WorkChart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\WorkChartRequest\StoreWorkChartRequest;
use App\Http\Requests\Settings\WorkChartRequest\UpdateWorkChartRequest;
use App\Service\Settings\WorkChartService\WorkChartService;
use Illuminate\Http\JsonResponse;


class WorkChartController extends Controller
{
    public function __construct(
        public WorkChartService $chartService,
    )
    {}


    public function index():JsonResponse
    {
        $response = $this->chartService->all();
        return $this->response(
            'Success',
            $response
        );
    }

    public function store(StoreWorkChartRequest $request):JsonResponse
    {
        $response = $this->chartService->create($request->toDto());
        return $this->response(
            'Success',
            $response
        );
    }

    public function show($id):JsonResponse
    {
        $response = $this->chartService->show($id);
        return $this->response(
            'Success',
            $response
        );
    }

    public function update(UpdateWorkChartRequest $request, $id):JsonResponse
    {
        $response = $this->chartService->update($request->toDto(),$id);
        return $this->response(
            'Success',
            $response
        );
    }

    public function destroy($id):JsonResponse
    {
        $response = $this->chartService->delete($id);
        return $this->response(
            'Success',
            $response
        );
    }
}
