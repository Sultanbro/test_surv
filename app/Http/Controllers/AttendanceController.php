<?php

namespace App\Http\Controllers;

use App\DayType;
use App\Http\Requests\GetDayAttendanceRequest;
use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Service\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AttendanceController extends Controller
{
    /**
     * @var AttendanceService
     */
    public AttendanceService $service;

    public function __construct(AttendanceService $service)
    {
//        abort_if(!auth()->user()->can('hr_view'), Response::HTTP_FORBIDDEN, 'Доступ закрыт 403');
//        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * @param StoreAttendanceRequest $request
     * @return JsonResponse
     */
    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $response = $this->service->storeAttendance($request);

        return response()->json($response);
    }

    /**
     * @param UpdateAttendanceRequest $request
     * @param DayType $attendance
     * @return JsonResponse
     */
    public function update(UpdateAttendanceRequest $request, DayType $attendance): JsonResponse
    {
        $response = $this->service->updateAttendance($attendance, $request);

        return response()->json($response);
    }

    /**
     * Получаем кол-во стажеров.
     * @param GetDayAttendanceRequest $request
     * @return JsonResponse
     */
    public function getDayAttendance(GetDayAttendanceRequest $request): JsonResponse
    {
        $response = $this->service->getAttendance($request);

        return response()->json($response);
    }

    /**
     * @param DayType $attendance
     * @return void
     */
    public function destroy(DayType $attendance): void
    {
        $attendance->delete();
    }
}
