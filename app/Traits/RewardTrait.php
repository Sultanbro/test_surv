<?php

namespace App\Traits;

use App\Exceptions\News\BusinessLogicException;
use App\Helpers\FileHelper;
use App\Http\Requests\RewardRequest;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Трейт отвечает за награждение пользователя.
 */
trait RewardTrait
{
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

    /**
     * @param $request
     * @return array|string[]
     * @throws BusinessLogicException
     */
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
}