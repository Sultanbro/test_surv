<?php

namespace App\Service\Award;

use App\Enums\AwardTypeEnum;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Http\Requests\Award\CourseAwardRequest;
use App\Http\Requests\GetCoursesAwardsRequest;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\SaveCoursesAwardsRequest;
use App\Http\Requests\StoreCoursesAwardsRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Models\Course;
use App\Models\CourseResult;
use App\Repositories\AwardRepository;
use App\Repositories\CoreRepository;
use App\User;
use Exception;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AwardService
{
    const POSITION = 'App\Position';
    const GROUP = 'App\ProfileGroup';

    /**
     * Место хранение в диске.
     * S3
     */
    public $disk;

    /**
     * Путь до файла.
     * Формат: jpg, png, pdf.
     */
    public $path;

    /**
     * @var CoreRepository
     */
    public CoreRepository $awardRepository;

    /**
     * @var CoreRepository
     */

    public function __construct()
    {
        $this->awardRepository     = app(AwardRepository::class);
        $this->disk = Storage::disk('s3');
        $this->path = 'awards/';
    }

    /**
     * @param RewardRequest $request
     * @param $awardRepository
     * @return mixed
     * @throws Exception
     */
    public function reward(RewardRequest $request, $awardRepository)
    {
        try {
            $awardId = $request->input('award_id');
            $userId  = $request->input('user_id');


            $award = $awardRepository->getById($awardId);
            if ($award->users()->where('user_id', $userId)->exists()){
                return response()->error('User has already  rewarded with this award!', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $file = $this->saveAwardFile($request);
            $added   = $awardRepository->attachUser($award, $userId, $file['relative']);

            return response()->success($added);
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
    public function delete(Award $award)
    {
        try {
            if (FileHelper::checkFile($award->path)){
                FileHelper::delete($award->path, $this->path);
            }

            return response()->success($award->delete());
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }


    /**
     * @param RewardRequest $request
     * @param $awardRepository
     * @return mixed
     * @throws Exception
     */
    public function deleteReward(RewardRequest $request, $awardRepository)
    {
        try {
            $awardId = $request->input('award_id');
            $userId  = $request->input('user_id');
            $added   = $awardRepository->detachUser($awardId, $userId);

            return response()->success($added);
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function myAwards($user_id): array
    {
        $user = User::query()->findOrFail($user_id);
        try {
            $awards = [];
            $awards['awards']['my']   = $this->awardRepository->relationAwardUser($user );
            $awards['types'] = $this->awardTypeRepository->allTypes();

            $awards['awards']['all'] =  $this->awardRepository->relationAwardUser($user,'!=' );

            return $awards;

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
    /**
     * @throws Exception
     */
    public function courseAward(CourseAwardRequest $request, $user): array
    {
        try {
            return Course::query()
                ->with(['award', 'course_results'=> function($query) use($user) {
                    $query->whereNotNull('ended_at')
                    ->where('user_id', $user->id)
                    ->where('status', CourseResult::COMPLETED)
                    ->with('user');
                }])
                ->findOrFail($request->input('course_id'))
                ->toArray();

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function courseAwards(GetCoursesAwardsRequest $request): array
    {
        try {
            return CourseResult::whereHas('user', function ($query){
                $query->whereHas('description', function ($q) {
                    $q->where('is_trainee', 0)
                    ->whereNull('fired');
                })->where('users.deleted_at', null);
            })
                ->whereNotNull('ended_at')
                ->where('status',CourseResult::COMPLETED)
                ->with(['user' => function($q){
                    $q->select('id', 'name', 'last_name');
                },'course' => function($q) {
                    $q->select('id', 'name', 'text');
                }])
                ->whereIn('course_id', $request->input('course_ids', []))
                ->select('id','ended_at', 'status', 'user_id', 'course_id')
                ->get()
                ->toArray();


        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function saveCourseAwards(StoreCoursesAwardsRequest $request, Award $award): array
    {
        try {
            if (!$request->hasFile('file')) {
                return [];
            }
            $files = [];
            if (AwardCategory::query()
                    ->findOrFail($award->award_category_id)->type != AwardTypeEnum::CERTIFICATE){
                throw new BusinessLogicException('Award must be of type certificate');
            }

            foreach ($request->file('file') as $file) {

                $originalName = $file->getClientOriginalName();

                $data = explode('_', $originalName);// here name is course_user_somename
                $courseId = null;
                $userId = null;
                if (isset($data[0]) && Course::query()->find($data[0])->exists()){
                    $courseId = (int)$data[0];
                }

                if (isset($data[1]) && User::query()->find($data[1])->exists()){
                    $userId = (int)$data[1];
                }

                if (!($userId && $courseId)){
                    continue;
                }

                if ($award->courses()->where('course_id', $courseId)->wherePivot('user_id', $userId)->exists()){
                    $path = $award->courses()->where('course_id', $courseId)->wherePivot('user_id', $userId)->get()->pluck('pivot.path');
                    if (FileHelper::checkFile($path)){
                        FileHelper::delete($path, $this->path);
                    }
                    $this->awardRepository->detachUserCourse($award->id, $courseId, $userId);
                }



                if (!$filename = FileHelper::save($file, $this->path)) {
                        throw new BusinessLogicException(__('exception.save_error'));
                    }

                    $this->awardRepository->attachUserCourse($award, $courseId, $userId, $filename, $file->getClientOriginalExtension() );

                    $files[] = [
                        'relative' => $filename,
                        'format' => $file->getClientOriginalExtension(),
                        'temp' => FileHelper::getUrl($this->path, $filename),
                        'user_id' => $userId,
                        'course_id' => $courseId
                    ];



            }

            return $files;



        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }



    private function saveAwardFile($request): array
    {
        if (!$request->hasFile('file')){
             return [
                'relative'  => '',
                'temp'      => ''
            ];
        }

        $file = $request->file('file');
        if (!$filename = FileHelper::save($file, $this->path)) {
            throw new BusinessLogicException(__('exception.save_error'));
        }
        return [
            'relative' => $filename,
            'format' => $file->getClientOriginalExtension(),
            'temp' => FileHelper::getUrl($this->path, $filename)
        ];
    }

    /**
     * @param int|null $categoryId
     * @param Award|null $award
     * @return string
     */
    public function getAwardType(int $categoryId = null, Award $award = null): string{
        $type = null;

        if ($award){
            $type = $award->category->type;
        }

        if ($categoryId){
            $type = AwardCategory::query()
                ->findOrFail($categoryId)->type;
        }

        return AwardTypeEnum::VALUES[$type];

    }

    /**
     * @param User $user
     * @return void
     */
    public function read(User $user): void
    {
        $groups = $user->groups->pluck('id')->toArray();

        $awards = Award::where(function ($q) use ($user, $groups) {
            $q->orWhere(function ($qu) use ($user) {
                $qu->where('targetable_id',$user->position_id)
                    ->where('targetable_type', self::POSITION);
            })
            ->orWhere(function ($qu) use ($groups) {
                $qu->whereIn('targetable_id', $groups)
                    ->where('targetable_type', self::GROUP);
            })
            ->orWhere(function ($qu) use ($user) {
                $qu->where('targetable_id', $user->getKey())
                    ->where('targetable_type', $user::class);
            });
        });

        $awards_ids = $awards->pluck('id')->toArray();
        Award::whereIn('id', $awards_ids)->update(['read' => true]);
    }
 }