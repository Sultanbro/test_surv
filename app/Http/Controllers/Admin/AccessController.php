<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\SwitchAccessRequest;
use App\Service\Permissions\AccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function __construct(
        private AccessService $accessService
    )
    {
    }

    /**
     * @OA\Post(
     *     summary="Attach or update user access for left bar",
     *     path="/profile/access",
     *     description="Attach or update user access for left bar"
     *     @OA\Response(
     *          response=200,
     *          description="Successfully created"
     *      ),
     * )
     *
     * @param SwitchAccessRequest $request
     * @return JsonResponse
     */
    public function switchAccess(SwitchAccessRequest $request): JsonResponse
    {
        $dto = $request->toDto();
        $response = $this->accessService->handle($dto);

        return $this->response('Successfully created', $response);
    }
}
