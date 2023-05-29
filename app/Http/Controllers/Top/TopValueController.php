<?php

namespace App\Http\Controllers\Top;

use App\Http\Controllers\Controller;
use App\Http\Requests\Top\ArchiveUtilityRequest;
use App\Service\Top\ArchiveUtilityForGroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopValueController extends Controller
{
    /**
     * @param ArchiveUtilityRequest $request
     * @param ArchiveUtilityForGroupService $service
     * @return JsonResponse
     */
    public function archiveUtility(ArchiveUtilityRequest $request, ArchiveUtilityForGroupService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
