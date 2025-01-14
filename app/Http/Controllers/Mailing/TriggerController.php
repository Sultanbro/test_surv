<?php

namespace App\Http\Controllers\Mailing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trigger\AbsentInternshipRequest;
use App\Http\Requests\Trigger\ApplyEmployeeRequest;
use App\Service\Trigger\AbsentInternshipService;
use App\Service\Trigger\ApplyEmployeeService;
use App\Service\Trigger\FiredEmployeeService;
use App\User;
use Illuminate\Http\JsonResponse;
use Throwable;

class TriggerController extends Controller
{
    /**
     * @param ApplyEmployeeRequest $request
     * @param ApplyEmployeeService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function applyEmployee(ApplyEmployeeRequest $request, ApplyEmployeeService $service): JsonResponse
    {
        return $this->response(
            message: 'Successfully applied',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param AbsentInternshipRequest $request
     * @param AbsentInternshipService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function absentInternship(AbsentInternshipRequest $request, AbsentInternshipService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param int $userId
     * @param FiredEmployeeService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function firedEmployee(int $userId, FiredEmployeeService $service): JsonResponse
    {
        User::getUserById($userId);

        return $this->response(
            message: 'Successfully fired',
            data: $service->handle($userId)
        );
    }
}
