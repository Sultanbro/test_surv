<?php

namespace App\Http\Controllers\Settings;

use App\Facade\Referring;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\GetUserRequest;
use App\Http\Requests\Settings\SettingUserRequest;
use App\Http\Requests\Settings\StoreUserRequest;
use App\Http\Requests\Settings\UpdateUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Service\Settings\UserService;
use App\Service\Settings\UserUpdateService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(
        public UserService       $userService,
        public UserUpdateService $updateService
    )
    {
    }

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
        $response = $this->userService->get($request->all());

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

        $response = $this->userService->userStore($request->toDto());

        return $this->response(
            message: "User stored successfully",
            data: new UserResource($response),
            code: Response::HTTP_CREATED,
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
     * @param SettingUserRequest $request
     * @return JsonResponse
     */
    public function create(SettingUserRequest $request): JsonResponse
    {
        $user = auth()->user() ?? User::query()->findOrFail(5);
        abort_if(!$user->can('users_view'), Response::HTTP_FORBIDDEN, 'Access denied');

        View::share('title', 'Новый сотрудник');
        View::share('menu', 'timetrackingusercreate');

        $response = $this->userService->userSetting($request->toDto()->userId);

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
     * @param UpdateUserRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = auth()->user();
        abort_if(!$user, Response::HTTP_FORBIDDEN, 'Access denied');

        $response = $this->updateService->updateUser($request->toDto());

        return $this->response(
            message: 'Successfully Updated',
            data: $response
        );
    }

    /**
     * @OA\Get (
     *     summary="Edit User",
     *     path="/timetracking/edit-person",
     *     description="Edit user in settings page"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     * @param SettingUserRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function edit(SettingUserRequest $request): JsonResponse
    {
        $user = auth()->user() ?? User::query()->findOrFail(5);
        abort_if(!$user->can('users_view'), Response::HTTP_FORBIDDEN, 'Access denied');

        View::share('title', 'Редактировать сотрудника');
        View::share('menu', 'timetrackingusercreate');

        $response = $this->userService->userSetting($request->toDto()->userId);

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
