<?php

namespace App\Service\Award;

use App\Http\Requests\RewardRequest;
use App\Http\Requests\StoreAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Models\Award;
use App\Models\AwardType;
use App\Repositories\AwardRepository;
use App\Repositories\CoreRepository;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AwardService
{
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


    public function __construct()
    {
        $this->disk = Storage::disk('s3');
        $this->path = 'awards/';
    }

    /**
     * Сохраняем тип награды.
     * @param $request
     * @return mixed
     */
    public function storeAwardType($request): mixed
    {
        try {
            $type = AwardType::query()->create($request->all());

            return response()->success($type);
        }catch (\Exception $exception) {
            return response()->error($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param $request
     * @param AwardType $awardType
     * @return bool
     * @throws Exception
     */
    public function updateAwardType($request, AwardType $awardType): bool
    {
        try {
            return $awardType->update($request->all());
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param StoreAwardRequest $request
     * @return mixed
     * @throws Exception
     */
    public function storeAward(StoreAwardRequest $request)
    {
        try {
            $success = Award::query()->create([
                'award_type_id' => $request->input('award_type_id'),
                'course_id' => $request->input('course_id'),
                'format'    => $request->file('image')->extension(),
                'icon'      => $request->input('icon'),
                'path'      => $this->saveAwardFile($request)['relative']
            ]);

            return response()->success($success);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function updateAward(UpdateAwardRequest $request, Award $award): bool
    {
        try {
            $parameters = $request->except('_method');
            if ($request->hasFile('file')) {
                if ($award->path != '') {
                    if($this->disk->exists($award->path)) {
                        $this->disk->delete($award->path);
                    }
                }

                $parameters['format'] = $request->file('file')->extension();
                $parameters['path']   = $this->saveAwardFile($request)['relative'];

                unset($parameters['file']);
            }
            return $award->update($parameters);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function myAwards($user): array
    {
        try {
            $awardRepository = app(AwardRepository::class);
            $awards = [];
            $access = $this->showOtherAwards($user);
            $awards['myAwards']   = $awardRepository->relationAwardUser($user);
            $awards['nomination'] = $awardRepository->getNomination();
            if ($access) {
                $awards['otherAwards'] = $awardRepository->relationAwardUser($user, '!=');
            }

            return $awards;

        } catch (\Throwable $exception) {
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
            $added   = $awardRepository->attachUser($awardId, $userId);

            return response()->success($added);
        }catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Проверяем есть ли у сотрудника доступ, чтоб смотреть награды других.
     * @param $user
     * @return bool
     */
    private function showOtherAwards($user): bool
    {
        return $user->user_description->view_other_awards == 1;
    }

    /**
     * @param $request
     * @return array
     */
    private function saveAwardFile($request): array
    {
        $extension  = $request->file('file')->extension();
        $awardFileName = uniqid() . '_' . md5(time()) . '.' . $extension;

        $this->disk->putFileAs($this->path , $request->file('file'), $awardFileName);
        $xpath = $this->path . $awardFileName;

        return [
            'relative'  => $xpath,
            'temp'      => $this->disk->temporaryUrl(
                $xpath, now()->addMinutes(360)
            )
        ];
    }
}