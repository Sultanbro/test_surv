<?php

namespace App\Http\Controllers\Mailing;

use App\Enums\Mailing\MailingEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trigger\ApplyEmployeeRequest;
use App\Models\Mailing\MailingNotification;
use App\Service\Trigger\ApplyEmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriggerController extends Controller
{
    public function applyEmployee(ApplyEmployeeRequest $request, ApplyEmployeeService $service): JsonResponse
    {
        return $this->response(
            message: 'Successfully applied',
            data: $service->handle($request->toDto())
        );
    }
}
