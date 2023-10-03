<?php

namespace App\Http\Controllers\Settings\Award;

use App\Http\Controllers\Controller;
use App\Http\Requests\Award\StoreAwardCategoryRequest;
use App\Http\Requests\Award\UpdateAwardCategoryRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Service\Award\AwardCategoryService;
use App\Service\Award\AwardService;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AwardCategoryController extends Controller
{
    /**
     * @var AwardCategoryService
     */
    private AwardCategoryService $awardCategoryService;

    public function __construct(AwardCategoryService $awardCategoryService)
    {
        $this->awardCategoryService = $awardCategoryService;
    }

    /**
     * Все типы награды.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $awardTypes = AwardCategory::with('creator')->get();
            return response()->success($awardTypes);
        } catch (\Exception $exception) {
            return response()->error($exception->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }



    /**
     * @param Award $award
     * @return mixed
     * @throws Exception
     */
    public function show(AwardCategory $awardCategory)
    {
        try {
            $this->access();
            return response()->success($awardCategory);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param StoreAwardCategoryRequest $request
     * @return mixed
     */
    public function store(StoreAwardCategoryRequest $request): mixed
    {
        $params = $request->validated();
        $params['created_by'] = \Auth::id() ?? 5;
        return $this->awardCategoryService->storeAwardCategory($params);
    }

    /**
     * @param UpdateAwardCategoryRequest $request
     * @param AwardCategory $awardCategory
     * @return mixed
     * @throws Exception
     */
    public function update(UpdateAwardCategoryRequest $request, AwardCategory $awardCategory): mixed
    {
        $params = $request->validated();
        $params['created_by'] = \Auth::id() ?? 5;
        $response = $this->awardCategoryService->updateAwardCategory($params, $awardCategory);

        return response()->success($response);
    }

    /**
     * @param AwardCategory $awardCategory
     * @return mixed
     * @throws Exception
     */
    public function destroy(AwardCategory $awardCategory): mixed
    {
        try {
            return response()->success($awardCategory->delete());
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param AwardCategory $awardCategory
     * @return mixed
     * @throws Exception
     */
    public function categoryAwards(AwardCategory $awardCategory)
    {
        try {
            $awards = $awardCategory->awards()->where('type', Award::TYPE_PUBLIC)->get();
            return response()->json($awards);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

}