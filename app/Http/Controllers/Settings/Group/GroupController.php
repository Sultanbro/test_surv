<?php
declare(strict_types=1);

namespace App\Http\Controllers\Settings\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\DeleteGroupRequest;
use App\Http\Requests\Group\RestoreGroupRequest;
use App\Http\Requests\Group\StoreGroupRequest;
use App\Service\Timetrack\GroupService;
use Exception;
use Illuminate\Http\JsonResponse;

final class GroupController extends Controller
{
    public function __construct(public GroupService $service)
    {}

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function get(): JsonResponse
    {
        $response = $this->service->getGroups();
        return response()->success($response);
    }

    /**
     * @Post{
     *  "name": "Admins"
     * }
     *
     * @param StoreGroupRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(StoreGroupRequest $request): JsonResponse
    {
        $response = $this->service->store($request->toDto()->name);
        return response()->success($response);
    }

    /**
     * @Post{
     *  "group": 26
     * }
     *
     * @param DeleteGroupRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function deactivate(DeleteGroupRequest $request): JsonResponse
    {
        $response = $this->service->delete($request->toDto()->id);
        return response()->success($response);
    }

    /**
     * @Post {
     *  "id": 22
     * }
     *
     * @param RestoreGroupRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function restore(RestoreGroupRequest $request): JsonResponse
    {
        $response = $this->service->retoreGroup($request->toDto()->id);
        return response()->success($response);
    }
}
