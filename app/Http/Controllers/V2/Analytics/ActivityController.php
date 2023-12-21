<?php

namespace App\Http\Controllers\V2\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\V2\Analytics\EditActivityRequest;
use App\Service\V2\Analytics\EditActivityService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ActivityController extends Controller
{
    /**
     * @param EditActivityRequest $request
     * @param EditActivityService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit(EditActivityRequest $request, EditActivityService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }
}
