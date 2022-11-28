<?php

namespace App\Http\Controllers\Award;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAwardTypeRequest;
use App\Http\Requests\UpdateAwardTypeRequest;
use App\Models\Award\AwardType;
use App\Service\Awards\AwardService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AwardTypeController extends Controller
{
    /**
     * @var AwardService
     */
    private AwardService $awardService;

    public function __construct(AwardService $awardService)
    {
        $this->access();
        $this->awardService = $awardService;
        $this->middleware('auth');
    }

    /**
     * Все типы награды.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $awardTypes = AwardType::all();
            return response()->success($awardTypes);
        } catch (\Exception $exception) {
            return response()->error($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param StoreAwardTypeRequest $request
     * @return mixed
     */
    public function store(StoreAwardTypeRequest $request): mixed
    {
        return $this->awardService->storeAwardType($request);
    }

    /**
     * @param UpdateAwardTypeRequest $request
     * @param AwardType $awardType
     * @return mixed
     * @throws Exception
     */
    public function update(UpdateAwardTypeRequest $request, AwardType $awardType): mixed
    {
        $response = $this->awardService->updateAwardType($request, $awardType);

        return response()->success($response);
    }

    /**
     * @param AwardType $awardType
     * @return mixed
     * @throws Exception
     */
    public function destroy(AwardType $awardType): mixed
    {
        try {
            return response()->success($awardType->delete());
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
