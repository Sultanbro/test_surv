<?php

namespace App\Http\Controllers\Timetrack;

use App\Http\Requests\TimeTrack\SalaryWorkChart\SalaryWorkChartRequest;
use App\Service\Timetrack\SalaryWorkChart\SalaryWorkChartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalaryWorkChartController extends Controller
{
    public function __construct(
        public SalaryWorkChartService $salaryWorkChartService,
    )
    {
        $this->middleware('auth');
    }

    public function salaryBalance(SalaryWorkChartRequest $request):JsonResponse
    {
        $response = $this->salaryWorkChartService->salaryBalance($request->toDto());
        return $this->response(
            'Success',
            $response
        );
    }
}