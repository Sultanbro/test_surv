<?php

namespace App\Service\Award;

use App\Http\Requests\AwardsByTypeRequest;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\StoreAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Models\Award;
use App\Models\AwardType;
use App\Models\AwardUser;
use App\Models\Course;
use App\Models\CourseResult;
use App\Repositories\AwardRepository;
use App\Repositories\AwardTypeRepository;
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

    /**
     * @var CoreRepository
     */
    public CoreRepository $awardRepository;

    /**
     * @var CoreRepository
     */
    public CoreRepository $awardTypeRepository;

    public function __construct()
    {
        $this->awardRepository     = app(AwardRepository::class);
        $this->awardTypeRepository = app(AwardTypeRepository::class);
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

            $file = $this->saveAwardFile($request);
            $success = Award::query()->create([
                'award_type_id' => $request->input('award_type_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'hide' => $request->input('hide'),
                'styles' => $request->input('styles'),
                'format'    => $request->file('file')->extension(),
                'icon'      => $request->input('icon'),
                'path'      => $file['relative']
            ]);
            $success['temp'] = $file['temp'];
            if ($request->has('course_ids')){
                Course::whereIn('id', $request->input('course_ids'))
                    ->update(['award_id' => $success->id]);
            }



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
            $awards = [];
            $access = $this->showOtherAwards($user);
            $awards['awards']['my']   = $this->awardRepository->relationAwardUser($user);
            $awards['awards']['nomination'] = $this->awardRepository->getNomination($user);
            $awards['types'] = $this->awardTypeRepository->allTypes();

            if ($access) {
                $awards['awards']['all'] = $this->awardRepository->relationAwardUser($user,null,'!=' );
            }

            return $awards;

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function awardsByType(AwardsByTypeRequest $request, int $user_id): array
    {
        //nominations - all, my, available //all if hidden false
        //certificates - all, my, available
        //nachisleniya - all by group, or position

        $user = User::query()->findOrFail($user_id);
        try {
            $result = [];
            $awardType = AwardType::query()->findOrFail($request->input('award_type_id'))->first();

            $userAwards = $user->awards;
            $availableAwards = $awardType->awards;
            if ($this->isNomination($awardType)) {
                $result['my'] =  $userAwards;
                $result['available'] =  $availableAwards ;
                if (!$awardType->hidden){
                    $result['other'] =   AwardUser::query()
                        ->whereNot('user_id', $user_id)
                        ->with(['user', 'award'])
                        ->get();;
                }
            }

            if ($this->isCertificate($awardType)){
                $otherAwards = [];
                foreach ($userAwards as $award){
                    $award['course'] = CourseResult::query()
                        ->where('user_id', $user_id)
                        ->whereNotNull('ended_at')
                        ->with('course')
                        ->get()
                        ->pluck('course');

                    if (!$award->hide){
                        $otherAwards = AwardUser::query()
                            ->whereNot('user_id', $user_id)
                            ->with(['user', 'award'])
                            ->get();;
                    }
                }
                $result['my'] =  $userAwards;
                $result['available'] =  $availableAwards;
                $result['other'] =  $otherAwards;

            }

            if ($this->isAccrual($awardType)){

                $result = $this->calculateSalaries();
            }


            return $result;

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function calculateSalaries(){

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


    private function isNomination($award): bool
    {
        return str_contains(strtolower($award->name), 'номинации');
    }
    private function isCertificate($award): bool
    {
        return str_contains(strtolower($award->name), 'сертификаты');
    }
    private function isAccrual($award): bool
    {
        return str_contains(strtolower($award->name), 'начисления');
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