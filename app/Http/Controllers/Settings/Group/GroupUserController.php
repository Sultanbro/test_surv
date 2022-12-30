<?php
declare(strict_types=1);

namespace App\Http\Controllers\Settings\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupUser\GetUsersRequest;
use App\Http\Requests\GroupUser\SaveUsersRequest;
use App\Service\Timetrack\Group\GetUserService;
use App\Service\Timetrack\Group\StoreUserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

final class GroupUserController extends Controller
{
    public function __construct(
        public GetUserService $getUserService,
        public StoreUserService $storeUserService
    )
    {}

    /**
     * @Post{
     *  "id": null|int
     *  "id": 26
     * }
     *
     * @param GetUsersRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function get(GetUsersRequest $request): JsonResponse
    {
        $type = isset($request->toDto()->id) ? 'group' : 'default';
        $response = $this->getUserService->handle($type, $request->toDto()->id);
        return response()->success($response);
    }

    /**
     * @OA\Post(
     *     summary="Store user in group",
     *     path="/timetracking/users/group/save",
     *     description="For store multiple users in group_user"
     *     @OA\Response(
     *          response=200,
     *          description="Successfully saved"
     *     ),
     * )
     *
     * @param SaveUsersRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function save(SaveUsersRequest $request): JsonResponse
    {
        $response = $this->storeUserService->handle($request->toDto());

        return $this->response(
            message: 'Successfully saved',
            data: $response
        );
    }
}
