<?php

namespace App\Http\Controllers\Article\Uploads;

use App\Exceptions\News\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Uploads\UploadRequest;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Http\Resources\Uploads\UploadResource;
use App\Service\Uploads\UploadService;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{
    public function __construct(protected UploadService $service)
    {
    }

    /**
     * @param UploadRequest $request
     * @return JsonResponse
     */
    public function store(UploadRequest $request): JsonResponse
    {
        return response()->json(
            new JsonSuccessResponse(
                __('upload.store'),
                (new UploadResource($this->service->store($request->getFile())))->toArray($request)
            )
        );
    }
}
