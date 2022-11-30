<?php

namespace App\Http\Controllers\Award;

use App\Enums\AwardTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User;
use App\Http\Requests\Award\AwardsByTypeRequest;
use App\Http\Requests\Award\CourseAwardRequest;
use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Http\Requests\RewardRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Repositories\AwardRepository;
use App\Service\Awards\AwardBuilder;
use App\Service\Awards\AwardService;
use Exception;
use Illuminate\Support\Facades\Auth;

class AwardController extends Controller
{
    /**
     * @var AwardService
     */
    private AwardService $awardService;
    private AwardBuilder $awardBuilder;

    public function __construct(AwardService $awardService)
    {
        $this->awardService = $awardService;
        $this->awardBuilder = app(AwardBuilder::class);
        $this->middleware('auth');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        try {
            $this->access();
            $awardTypes = Award::all();
            return response()->success($awardTypes);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param StoreAwardRequest $request
     * @return mixed
     * @throws Exception
     */
    public function store(StoreAwardRequest $request)
    {
        $this->access();
        $type = $this->awardService->getAwardType($request->input('award_category_id'));

        $response = $this->awardBuilder
            ->handle($type)
            ->store($request);

        return response()->success($response);
    }

    /**
     * @param UpdateAwardRequest $request
     * @param Award $award
     * @return mixed
     * @throws Exception
     */
    public function update(Award $award, UpdateAwardRequest $request )
    {
        $this->access();
        $type = $this->awardService->getAwardType($request['award_category_id'], $award);
        $response = $this->awardBuilder
            ->handle($type)
            ->update($request, $award);

        return response()->success($response);
    }

    /**
     * @throws Exception
     */
    public function destroy(Award $award)
    {
        try {
//            $this->access();
            return response()->success($this->awardService->delete($award));
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }


    /**
     * @throws Exception
     */
    public function reward(RewardRequest $request, AwardRepository $awardRepository)
    {
        $this->access();
        return $this->awardService->reward($request, $awardRepository);
    }

    public function deleteReward(RewardRequest $request, AwardRepository $awardRepository)
    {
        $this->access();
        return $this->awardService->deleteReward($request, $awardRepository);
    }

    /**
     * @throws Exception
     */
    public function myAwards()
    {
        $authUser = Auth::user() ?? User::find(13865);
        $response = $this->awardService->myAwards($authUser);

        return \response()->success($response);
    }

    /**
     * @throws Exception
     */
    public function courseAward(CourseAwardRequest $request)
    {
        $response = $this->awardService->courseAward($request);


        return \response()->success($response);
    }

      /**
     * @throws Exception
     */
    public function awardsByType(AwardsByTypeRequest $request)
    {
        $params = $request->validated();
        $params['user_id'] = $request->get('user_id', Auth::id() ?? 5);


        $response = $this->awardBuilder->handle($params['key'])->fetch($params);

        return \response()->success($response);
    }




}
