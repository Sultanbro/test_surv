<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\CreateUserRequest;
use App\Http\Requests\Settings\GetUserRequest;
use App\Http\Requests\Settings\StoreUserRequest;
use App\Service\Settings\UserService;
use App\Service\Settings\UserUpdateService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use function Termwind\terminal;

class UserController extends Controller
{
    public function __construct(
        public UserService $service,
        public UserUpdateService $updateService
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

    /**
     * @OA\Post(
     *     summary="Store User",
     *     path="/timetracking/person/store",
     *     description="Store user in settings page"
     *     @OA\Response(
     *          response=200,
     *          description="User stored successfully"
     *      ),
     * )
     * @param StoreUserRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = auth()->user() ?? User::query()->findOrFail(5);
        abort_if(!$user->can('users_view'), Response::HTTP_FORBIDDEN, 'У вас нет доступа!');

        $response = $this->service->userStore($request->toDto());

        return $this->response(
            message: "User stored successfully",
            data: $response
        );
    }

    /**
     * @OA\Get(
     *     summary="Create User",
     *     path="/timetracking/create-person",
     *     description="Create user in settings page"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function create(CreateUserRequest $request): JsonResponse
    {
        $user = auth()->user() ?? User::query()->findOrFail(5);
        abort_if(!$user->can('users_view'), Response::HTTP_FORBIDDEN, 'Access denied');

        View::share('title', 'Новый сотрудник');
        View::share('menu', 'timetrackingusercreate');

        $response = $this->service->createUser($request->toDto()->userId);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    /**
     * @OA\Put(
     *     summary="Update User",
     *     path="/timetracking/update-person",
     *     description="Update user in settings page"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     * @param StoreUserRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(StoreUserRequest $request): JsonResponse
    {
        $user = auth()->user() ?? User::query()->find(5);
        abort_if(!$user, Response::HTTP_FORBIDDEN, 'Access denied');

        $response = $this->updateService->updateUser($request->toDto());

        return $this->response(
            message: 'Successfully Updated',
            data: $response
        );
    }
}
