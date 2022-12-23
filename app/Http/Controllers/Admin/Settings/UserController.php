<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\GetUserRequest;
use App\Http\Requests\Settings\StoreUserRequest;
use App\Service\Settings\UserService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function store(StoreUserRequest $request)
    {
        $user = auth()->user() ?? User::query()->findOrFail(5);
        abort_if(!$user->can('users_view'), Response::HTTP_FORBIDDEN, 'У вас нет доступа!');

        $response = $this->service->userStore($request->toDto());

        return $this->response(
            message: "User stored successfully",
            data: $response
        );
    }
}
