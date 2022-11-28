<?php

namespace App\Service\Award;

use App\Http\Requests\AwardsByTypeRequest;
use App\Http\Requests\CourseAwardRequest;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\StoreAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Models\Admin\ObtainedBonus;
use App\Models\Award;
use App\Models\AwardType;
use App\Models\Course;
use App\Models\CourseResult;
use App\Position;
use App\ProfileGroup;
use App\Repositories\AwardRepository;
use App\Repositories\AwardTypeRepository;
use App\Repositories\CoreRepository;
use App\Salary;
use App\SavedKpi;
use App\Service\Department\UserService;
use App\User;
use AWS\CRT\Log;
use Carbon\Carbon;
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
            $awardType = AwardType::query()
                ->findOrFail($request->input('award_type_id'));


            if ($this->isCertificate($awardType) && !$request->has('course_ids')){
                return response()->error('Course required for certificate award', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $params = [
                'award_type_id' => $request->input('award_type_id'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'hide' => $request->input('hide'),
                'styles' => $request->input('styles'),
                'icon'      => $request->input('icon'),
                'path'      => $file['relative']
            ];
            if ($this->isAccrual($awardType)){
                $params = [
                    'award_type_id' => $request->input('award_type_id'),
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'targetable_type'      => $request->input('targetable_type'),
                    'targetable_id'      => $request->input('targetable_id'),
                ];
            }

            $success = Award::query()->create($params);
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
            $parameters = $request->except(['_method', 'course_ids']);
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
            $this->updateCourses($request->input('course_ids', []), $award);


            return $award->update($parameters);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $course_ids
     * @param $award
     * @return void
     */
    public function updateCourses($courseIds, $award){
        $existingCourses = Course::where('award_id', $award->id)->get()->pluck('id');
        error_log(json_encode($existingCourses));
        error_log(json_encode($courseIds));
        $removeCourses = $existingCourses->diff(
            $courseIds
        );

        error_log(json_encode($removeCourses));
        if ($removeCourses){
            Course::whereIn('id', $removeCourses)
                ->update(['award_id' => 0]);
        }
        Course::whereIn('id', $courseIds)
            ->update(['award_id' => $award->id]);
    }

    /**
     * @throws Exception
     */
    public function myAwards($user_id): array
    {
        $user = User::query()->findOrFail($user_id);
        try {
            $awards = [];
            $access = $this->showOtherAwards($user);
            $awards['awards']['my']   = $this->awardRepository->relationAwardUser($user );
            $awards['types'] = $this->awardTypeRepository->allTypes();

            if ($access) {
                $awards['awards']['all'] =  $this->awardRepository->relationAwardUser($user,'!=' );
            }

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

    public function awardsByType(AwardsByTypeRequest $request, int $user_id): array
    {
        $user = User::query()->findOrFail($user_id);
        try {
            $result = [];
            $awardType = AwardType::query()->findOrFail($request->input('award_type_id'));

            $userAwards = $this->awardRepository->relationAwardUser($user,$awardType);
            $availableAwards =$this->awardRepository->availableAwards($user,$awardType);
            if ($this->isNomination($awardType)) {
                $result['my'] =  $userAwards;
                $result['available'] =  $availableAwards ;
                $otherAwards = [];

                foreach ($userAwards as $award){
                    if (!$award['hide']){
                        $otherAwards= $this->awardRepository->relationAwardUser($user,$awardType,'!=' );
                    }
                }
                $result['other'] =  $otherAwards;

            }

            if ($this->isCertificate($awardType)){
                $otherAwards = [];
                foreach ($userAwards as $key => $award){
                    $userAwards[$key]['course'] = CourseResult::query()
                        ->where('user_id', $user_id)
                        ->whereNotNull('ended_at')
                        ->with('course', function ($q) use ($award){
                            $q->where('award_id', $award['award_id']);
                        })
                        ->get()
                        ->pluck('course');

                    if (!$award['hide']){
                        $otherAwards = $this->awardRepository->relationAwardUser($user,$awardType,'!=' );
                    }
                }
                $result['my'] =  $userAwards;
                $result['available'] =  $availableAwards;
                $result['other'] =  $otherAwards;

            }

            if ($this->isAccrual($awardType)){

                $result= $this->getAccrual($user, $awardType->id);

            }


            return $result;

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $user
     * @return array
     */
    public function getAccrual($user, $award_type_id): array
    {
        $result = [];
        $groups = $user->groups->pluck('id')->toArray();

        $today = Carbon::now();
        $date = Carbon::createFromDate($today->year, $today->month, 1);

        $awards = Award::query()
            ->where('award_type_id', $award_type_id)
            ->where(function ($query) use ($user, $groups) {
                $query->where(function ($q) use ($user) {
                    $q->where('targetable_id',$user->position_id)
                    ->where('targetable_type', self::POSITION);

                 })
                ->orWhere(function ($q) use ($groups) {
                    $q->whereIn('targetable_id', $groups)
                    ->where('targetable_type', self::GROUP);
                });
            })
            ->get()
            ->pluck('targetable_type','targetable_id');

        foreach ($awards as $targetable_id => $targetable_type){

            if ($targetable_type == self::GROUP){
                $user_ids = collect( (new UserService)
                    ->getEmployees($targetable_id, $date->format('Y-m-d')))
                    ->pluck('id')->toArray();
                $result['group'][$targetable_id] = $this->getTopSalaryEmployees($user_ids, $date, $targetable_id);
            }

            if ($targetable_type == self::POSITION){
                $user_ids = Position::query()
                    ->findOrFail($targetable_id)
                    ->users
                    ->pluck('id');
                $result['position'][$targetable_id] = $this->getTopSalaryEmployees($user_ids, $date, $groups[0]);

            }
        }

        return $result;
    }

    public function getTopSalaryEmployees($user_ids,Carbon $date,$group_id){
        $result = [];
        $month = $date->startOfMonth();
        $group = ProfileGroup::find($group_id);

        $users = Salary::getUsersData($month, $user_ids);

        $internship_pay_rate = $group->paid_internship == 1 ? 0.5 : 0;
        foreach ($users as $user){
            $userFot = $user->calculateFot($internship_pay_rate, $date);
            $result[] = [
             'kpi' => $userFot['kpi'],
             'earnings' => $userFot['earnings'],
             'bonuses' => $userFot['bonuses'],
                'total' => array_sum(array_values($userFot)),
                'position' => $user->position?->position,
             'name' => $user->name,
             'last_name' => $user->last_name,
                'path'=> 'users_img/' . $user->img_url,
             'email' => $user->email,
             'id' => $user->id,
            ];
        }
        //sort
        usort($result, function($a, $b) {
            if ($a['total'] == $b['total']) {
                return 0;
            }
            return ($a['total'] > $b['total']) ? -1 : 1;
        });

        //return top 3
        return array_slice( $result , 0, 3);
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
        if (!$request->hasFile('file')){
           return [
                'relative'  => '',
                'temp'      => ''
            ];
        }
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