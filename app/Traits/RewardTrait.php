<?php

namespace App\Traits;

use App\DTO\RewardDTO;
use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Http\Requests\RewardRequest;
use App\Models\Award\Award;
use App\Repositories\AwardRepository;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Трейт отвечает за награждение пользователя.
 */
trait RewardTrait
{
    /**
     * @param RewardDTO $dto
     * @param AwardRepository $awardRepository
     * @return mixed
     * @throws Exception
     */
    public function rewardUser(RewardDTO $dto,AwardRepository $awardRepository): mixed
    {
        try {
            $award = $awardRepository->getById($dto->awardId);
            if ($award->users()->where('user_id', $dto->userId)->exists()){
                return response()->error('User has already  rewarded with this award!', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $file = $this->saveAwardFile($dto);
            $preview = $this->saveAwardPreview($dto);

            if (!$file['relative']){
                $file = [
                    'relative'=> $award->path,
                    'format'=> $award->format,
                ];
            }

            if (!$preview['relative']){
                $preview = [
                    'relative'=> $award->preview_path,
                    'format'=> $award->preview_format,
                ];
            }
            $added = $awardRepository->attachUser($award, $dto->userId, $file, $preview);

            return response()->success($added);
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param RewardDTO $dto
     * @param AwardRepository $awardRepository
     * @return mixed
     * @throws Exception
     */
    public function rewardUserWitCourse(RewardDTO $dto, AwardRepository $awardRepository)
    {
        try {
            $award = $awardRepository->getById($dto->awardId);

            if ($award->courses()->where('course_id', $dto->courseId)->wherePivot('user_id', $dto->userId)->exists()){
                return response()->error('User has already  rewarded with this course!', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $file = $this->saveAwardFile($dto);
            $preview = $this->saveAwardPreview($dto);
            $added = $awardRepository->attachUserCourse($award, $dto->courseId, $dto->userId, $file, $preview);

            return response()->success($added);
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param RewardDTO $dto
     * @param AwardRepository $awardRepository
     * @return mixed
     * @throws Exception
     */
    public function deleteUserReward(RewardDTO $dto,AwardRepository $awardRepository)
    {
        try {
            $awardId = $dto->awardId;
            $userId  = $dto->userId;
            $added   = $awardRepository->detachUser($awardId, $userId);
            $award = $awardRepository->getById($awardId);

            if ($award->type == Award::TYPE_PERSONAL)
            {
                $award->delete();
            }

            return response()->success($added);
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param RewardDTO $dto
     * @param AwardRepository $awardRepository
     * @return mixed
     * @throws Exception
     */
    public function deleteRewardCertificate(RewardDTO $dto,AwardRepository $awardRepository)
    {
        try {
            $awardId = $dto->awardId;
            $userId  = $dto->userId;
            $courseId = $dto->courseId;
            $added   = $awardRepository->detachUserCourse($awardId, $courseId, $userId);

            return response()->success($added);
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $dto
     * @return array|string[]
     * @throws BusinessLogicException
     */
    private function saveAwardFile($dto): array
    {
        if (!$dto->file){
            return [
                'relative'  => '',
                'format'=> '',
                'temp'      => ''
            ];
        }
        $path = 'awards/';
        if (!$filename = FileHelper::save($dto->file, $path)) {
            throw new BusinessLogicException(__('exception.save_error'));
        }
        return [
            'relative' => $filename,
            'format' => $dto->file->getClientOriginalExtension(),
            'temp' => FileHelper::getUrl($path, $filename)
        ];
    }

    /**
     * @param $dto
     * @return array|string[]
     * @throws BusinessLogicException
     */
    private function saveAwardPreview($dto): array
    {
        if (!$dto->preview){
            return [
                'relative'  => '',
                'format'=> '',
                'temp'      => ''
            ];
        }
        $path = 'awards/';
        if (!$filename = FileHelper::save($dto->preview, $path)) {
            throw new BusinessLogicException(__('exception.save_error'));
        }
        return [
            'relative' => $filename,
            'format' => $dto->preview->getClientOriginalExtension(),
            'temp' => FileHelper::getUrl($path, $filename)
        ];
    }
}
