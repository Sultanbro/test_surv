<?php

namespace App\Http\Controllers\Article\Files;

use App\Exceptions\News\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Files\FileRequest;
use App\Http\Requests\Files\FileStoreRequest;
use App\Http\Resources\Files\FileResource;
use App\Http\Resources\Responses\JsonSuccessResponse;
use App\Service\Files\FileService;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    public function __construct(protected FileService $service)
    {
    }

    /**
     * @param FileStoreRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function store(FileStoreRequest $request): JsonResponse
    {
        return response()->json(
            new JsonSuccessResponse(
                __('model/file.store'),
                (new FileResource(
                    $this->service->store(
                        $request->getFile(),
                        'public'
                    )
                ))->toArray($request)
            )
        );
    }

    /**
     * @param FileRequest $request
     * @return JsonResponse
     * @throws BusinessLogicException
     */
    public function delete(FileRequest $request): JsonResponse
    {
        $this->service->delete($request->getFile());

        return response()->json(
            new JsonSuccessResponse(
                __('model/file.delete'),
            )
        );
    }
}
