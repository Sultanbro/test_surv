<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Http\Requests\StoreAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Service\Award\AwardService;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AwardController extends Controller
{
    /**
     * @var AwardService
     */
    private AwardService $awardService;

    public function __construct(AwardService $awardService)
    {
        $this->awardService = $awardService;
    }

    /**
     * @throws Exception
     */
    public function getAwards()
    {
        $authUser = Auth::user() ?? User::find(5);
        $response = $this->awardService->myAwards($authUser);

        return \response()->success($response);
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
        return $this->awardService->storeAward($request);
    }

    /**
     * @throws Exception
     */
    public function show(Award $award)
    {
        try {
            return response()->success($award);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * form-data: {
     * "award_type_id": 1,
     * "course_id":1,
     * "award_id":1,
     * "image": file
     * }
     * @param UpdateAwardRequest $request
     * @param Award $award
     * @return mixed
     * @throws Exception
     */
    public function update(UpdateAwardRequest $request, Award $award)
    {
        $response = $this->awardService->updateAward($request, $award);

        return response()->success($response);
    }

    /**
     * @throws Exception
     */
    public function destroy(Award $award)
    {
        try {
            return response()->success($award->delete());
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
