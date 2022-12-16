<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupUser\GetUsersRequest;
use App\Service\Timetrack\GetGroupUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GroupUserController extends Controller
{
    public function __construct(
        public GetGroupUserService $service
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
     */
    public function get(GetUsersRequest $request): JsonResponse
    {
        $type = isset($request->toDto()->id) ? 'group' : 'default';
        $response = $this->service->handle($type, $request->toDto()->id);
        return response()->success($response);
    }
}
