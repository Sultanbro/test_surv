<?php

namespace App\Http\Controllers\Award;

use App\Http\Controllers\Controller;
use App\Http\Requests\Award\StoreAwardCategoryRequest;
use App\Http\Requests\Award\UpdateAwardCategoryRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Service\Awards\AwardCategoryService;
use App\Service\Awards\AwardService;
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
//        $this->access();
        $this->awardCategoryService = $awardCategoryService;
//        $this->middleware('auth');
    }

    /**
     * Все типы награды.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $awardTypes = AwardCategory::all();
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
        return $this->awardCategoryService->storeAwardCategory($request);
    }

    /**
     * @param UpdateAwardCategoryRequest $request
     * @param AwardCategory $awardCategory
     * @return mixed
     * @throws Exception
     */
    public function update(UpdateAwardCategoryRequest $request, AwardCategory $awardCategory): mixed
    {
        $response = $this->awardCategoryService->updateAwardCategory($request, $awardCategory);

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
            $this->access();
            return response()->success($awardCategory->awards);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

}