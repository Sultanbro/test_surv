<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Position;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\AnyPositionRequest;
use App\Http\Requests\Position\GetPositionRequest;
use App\Http\Requests\Position\StorePositionWithDescriptionRequest;
use App\Service\Position\PositionService;
use Dflydev\DotAccessData\Data;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PositionController extends Controller
{
    public function __construct(
        public PositionService $service
    )
    {

    }
    /**
     * @Post{
     *  "position": "name"
     * }
     *
     * @param AnyPositionRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(AnyPositionRequest $request): JsonResponse
    {
        $response = $this->service->add($request->toDto()->position);
        return response()->success($response);
    }

    /**
     * @Get{
     *  "name": 30
     * }
     *
     * @param GetPositionRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function get(GetPositionRequest $request): JsonResponse
    {
        $response = $this->service->get($request->toDto()->id);
        return response()->json($response);
    }

    /**
     * @Delete{
     *  "position": "76"
     * }
     *
     * @param AnyPositionRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(AnyPositionRequest $request): JsonResponse
    {
        $response = $this->service->delete($request->toDto()->position);
        return response()->success($response);
    }

    /**
     * @Post{
     *  {
     *   "id": 46,
     *   "new_name": "name",
     *   "indexation": 1,
     *   "sum": 10,
     *   "desc": {
     *       "require": "text",
     *       "actions": "actions text",
     *       "time": "time",
     *       "salary": "salary",
     *       "knowledge": "knowledge",
     *       "next_step": "next",
     *       "show": 1
     *      }
     *   }
     * }
     *
     * @param StorePositionWithDescriptionRequest $request
     * @return void
     * @throws \Exception
     */
    public function savePositionWithDescription(StorePositionWithDescriptionRequest $request)
    {
        $dto = $request->toDto();
        $response = $this->service->savePositionWithDescription(
            $dto->id,
            $dto->newName,
            $dto->indexation,
            $dto->sum,
            $dto->description
        );

        return response()->success($response);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function all()
    {
        $response = $this->service->allPositions();
        return response()->success($response);
    }
}
