<?php
declare(strict_types=1);

namespace App\Http\Controllers\Settings\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupUser\GetUsersRequest;
use App\Http\Requests\GroupUser\SaveUsersRequest;
use App\Service\Timetrack\GetGroupUserService;
use Exception;
use Illuminate\Http\JsonResponse;

final class GroupUserController extends Controller
{
    public function __construct(GetGroupUserService $service)
    {
        
    }

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
        $response = $this->service->handle($type, $request->toDto()->id);
        return response()->success($response);
    }

    public function save(SaveUsersRequest $request)
    {
        dd('her');
    }
}
