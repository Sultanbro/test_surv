<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\GetUserRequest;
use App\Service\Settings\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        public UserService $service
    )
    {}

    /**
     * @OA\Post(
     *     summary="Get Users",
     *     path="/timetracking/get-persons",
     *     description="Get users for settings page"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     * ),
     * )
     *
     * @param GetUserRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function get(GetUserRequest $request): JsonResponse
    {
        $response = $this->service->get($request->all());

        return $this->response(
            message: "Success",
            data: $response
        );
    }
}
