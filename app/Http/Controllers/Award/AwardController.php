<?php

namespace App\Http\Controllers\Award;

use App\Enums\AwardTypeEnum;
use App\Http\Controllers\Controller;
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
use App\Service\Awards\Reward\RewardBuilder;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        try {
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
            return response()->success($this->awardService->delete($award));
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param Award $award
     * @return StreamedResponse
     * @throws Exception
     */
    public function downloadFile(Award $award)
    {
        try {
            return  Storage::disk('s3')->download('awards/' . $award->path);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }



    /**
     * @throws Exception
     */
    public function reward(RewardRequest $request, AwardRepository $repository)
    {
        $app = app(RewardBuilder::class);
        $app->handle($request->toDto(), $repository)->reward();
    }

    /**
     * @param RewardRequest $request
     * @param AwardRepository $repository
     * @return void
     */
    public function deleteReward(RewardRequest $request, AwardRepository $repository)
    {
        $app = app(RewardBuilder::class);
        $app->execute($request->toDto(), $repository)->deleteReward();
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
        $authUser = Auth::user() ?? User::find(5);

        $response = $this->awardService->courseAward($request, $authUser);


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
