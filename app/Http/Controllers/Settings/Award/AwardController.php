<?php

namespace App\Http\Controllers\Settings\Award;

use App\Exceptions\News\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Award\AwardsByTypeRequest;
use App\Http\Requests\Award\CourseAwardRequest;
use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Http\Requests\GetCoursesAwardsRequest;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\StoreCoursesAwardsRequest;
use App\Models\Award\Award;
use App\Repositories\AwardRepository;
use App\Service\Award\AwardBuilder;
use App\Service\Award\AwardService;
use App\Service\Award\AwardType\CertificateAwardService;
use App\Service\Award\Reward\RewardBuilder;
use App\Service\Interfaces\Award\AwardInterface;
use App\User;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

        return $this->awardBuilder
            ->handle($type)
            ->store($request);
    }

    /**
     * @param UpdateAwardRequest $request
     * @param Award $award
     * @return mixed
     * @throws Exception
     */
    public function update(Award $award, UpdateAwardRequest $request)
    {
        $type = $this->awardService->getAwardType($request['award_category_id'], $award);
        /** @var AwardInterface $service */
        $service = $this->awardBuilder->handle($type);
        $service->update($request, $award);

        return response()->success($service);
    }

    /**
     * @throws BusinessLogicException
     */
    public function addPreview(Request $request, CertificateAwardService $service): bool|int
    {
        $award = Award::query()->findOrFail($request->get('id'));

        $parameters = [];

        if ($request->has('preview')) {
            $preview = $service->saveAwardPreview($request);
            $parameters['preview_format'] = $preview['format'];
            $parameters['preview_path'] = $preview['relative'];
        }

        return $award->update($parameters);
    }

    /**
     * @throws BusinessLogicException
     */
    public function addPreviewSecond(Request $request, CertificateAwardService $service): int
    {
        $award = DB::table('award_course')
            ->where('user_id', $request->get('user_id'))
            ->where('award_id', $request->get('award_id'))
            ->where('course_id', $request->get('course_id'))
            ->where('path', $request->get('path'));

        $parameters = [];

        if ($request->has('preview')) {
            $preview = $service->saveAwardPreview($request);
            $parameters['preview_format'] = $preview['format'];
            $parameters['preview_path'] = $preview['relative'];
        }
        return $award->update($parameters);
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
    public function downloadFile(Award $award): StreamedResponse
    {
        try {
            return Storage::disk('s3')->download('awards/' . $award->path);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }


    /**
     * @param RewardRequest $request
     * @param AwardRepository $repository
     * @throws Exception
     */
    public function reward(RewardRequest $request, AwardRepository $repository): void
    {
        /** @var RewardBuilder $app */
        $app = app(RewardBuilder::class);
        $app->handle($request->toDto(), $repository)->reward();
    }

    /**
     * @param RewardRequest $request
     * @param AwardRepository $repository
     * @return void
     */
    public function deleteReward(RewardRequest $request, AwardRepository $repository): void
    {
        $app = app(RewardBuilder::class);
        $app->execute($request->toDto(), $repository)->deleteReward();
    }

    /**
     * @throws Exception
     */
    public function myAwards()
    {
        $authUser = Auth::user() ?? User::query()->find(13865);
        $response = $this->awardService->myAwards($authUser);

        return \response()->success($response);
    }

    /**
     * @throws Exception
     */
    public function courseAward(CourseAwardRequest $request)
    {
        $authUser = Auth::user() ?? User::query()->find(5);
        $response = $this->awardService->courseAward($request, $authUser);
        return \response()->success($response);
    }

    /**
     * @throws Exception
     */
    public function coursesAward(GetCoursesAwardsRequest $request)
    {
        $response = $this->awardService->courseAwards($request);
        return \response()->success($response);
    }

    /**
     * @throws Exception
     */
    public function storeCoursesAward(StoreCoursesAwardsRequest $request, Award $award)
    {
        $response = $this->awardService->saveCourseAwards($request, $award);
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


    /**
     *
     */
    public function read()
    {
        /** @var User $user */
        $user = User::query()->find(Auth::id());

        if (is_null($user)) {
            throw new HttpException("Такого пользователя не существует");
        }

        $this->awardService->read($user);

        return \response()->success([
            'status' => 'success',
        ]);
    }


    public function fixPreviewPage()
    {
        // пустая страница
        return view('newprofile');
    }
}
