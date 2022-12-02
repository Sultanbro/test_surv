<?php

namespace App\Service\Awards;

use App\Enums\AwardTypeEnum;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Http\Requests\Award\CourseAwardRequest;
use App\Http\Requests\RewardRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Models\Course;
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
    public function courseAward(CourseAwardRequest $request): array
    {
        try {
            return Course::query()
                ->findOrFail($request->input('course_id'))
                ->award
                ->toArray();

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
 }