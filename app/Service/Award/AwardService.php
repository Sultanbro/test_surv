<?php

namespace App\Service\Award;

use App\Http\Requests\AwardsByTypeRequest;
use App\Http\Requests\RewardRequest;
use App\Http\Requests\StoreAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Models\Admin\ObtainedBonus;
use App\Models\Award;
use App\Models\AwardType;
use App\Models\AwardUser;
use App\Models\Course;
use App\Models\CourseResult;
use App\ProfileGroup;
use App\Repositories\AwardRepository;
use App\Repositories\AwardTypeRepository;
use App\Repositories\CoreRepository;
use App\Salary;
use App\Service\Department\UserService;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AwardService
{
    const POSITION = 'App\Positions';
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
            $awardType = AwardType::query()->findOrFail($request->input('award_type_id'));

            $userAwards = $user->awards->where('award_type_id', $awardType->id);
            $availableAwards = $awardType->awards;
            if ($this->isNomination($awardType)) {
                $result['my'] =  $userAwards;
                $result['available'] =  $availableAwards ;
                $otherAwards = [];

                foreach ($userAwards as $award){
                    if (!$award->hide){
                        $otherAwards = AwardUser::query()
                            ->whereNot('user_id', $user_id)
                            ->with(['user', 'award'])
                            ->get();;
                    }
                }
                $result['other'] =  $otherAwards;

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

                $result['awards'] = $this->getAccrual($user, $awardType->id);

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
            ->orWhere('targetable_id',$user->position_id)
            ->orWhereIn('targetable_id', $groups)
            ->get()
            ->pluck('targetable_type','targetable_id');



        foreach ($awards as $targetable_id => $targetable_type){
            $user_ids = (new UserService)->getEmployees($targetable_id, $date->format('Y-m-d'));


            if ($targetable_type == self::GROUP){
                $result['topByGroup'] = $this->getTopSalaryEmployees($user_ids, $date, $targetable_id);
            }

            if ($targetable_type == self::POSITION){
                $result['topByPosition'] = $this->getTopSalaryEmployees($user_ids, $date, $targetable_id);

            }
        }

        error_log(json_encode($result));
        return $result;
    }

    public function getTopSalaryEmployees($user_ids, $date,$group_id){
        $result = [];
        $month = Carbon::parse($date)->startOfMonth();
        $group = ProfileGroup::find($group_id);

        $users = Salary::getUsersData($month, $user_ids);

        $internship_pay_rate = $group->paid_internship == 1 ? 0.5 : 0;
        foreach ($users as $user){
            $earningSum = 0;
            $bonusesSum = 0;

            $user_applied_at = $user->applied_at();
            $trainee_days = $user->daytypes->whereIn('type', [5,6,7]);
            $work_shift = $user->working_time_id == 1 ? 8 : 9;

            $tts_before_apply = $user->timetracking
                ->where('time', '<', Carbon::parse($user_applied_at)->timestamp);
            $tts = $user->timetracking
                ->where('time', '>=', Carbon::parse($user_applied_at)->timestamp);

            for ($i = 1; $i <= $month->daysInMonth; $i++) {
                $d = (strlen ($i) == 1) ?  '0' . $i  :  '' . $i;
                $daySalary = $user->salaries->where('day', $d)->first();

                // accrual
                $salary = $daySalary->amount ?? 70000;
                $working_hours = $user->workingTime->time ?? 9;
                $ignore = $user->working_day_id == 1 ? [6,0] : [0];
                $workdays = workdays($month->year, $month->month, $ignore);

                $hourly_pay = $salary / $workdays / $working_hours;

                $time_day = $tts->where('day', $i);
                $time_day_before_apply = $tts_before_apply->where('day', $i);
                $time_day_trainee = $trainee_days->where('day', $i);


                if($time_day_trainee->count() > 0) { // день отмечен как стажировка
                    $earningSum += round( $hourly_pay * $internship_pay_rate * $work_shift);

                }
                if($time_day->count() > 0) { // отработанное врея есть
                    $total_hours = $time_day->sum('total_hours');
                    $earningSum += round($total_hours / 60 * $hourly_pay);

                }
                if($time_day_before_apply->count() > 0) {// отработанное врея есть до принятия на работу
                    $total_hours = $time_day_before_apply->sum('total_hours');
                    $earningSum += round($total_hours / 60 * $hourly_pay);
                }



                //bonuses
                $bonusesSum += $daySalary?->bonus;

                //awards
                $award_date = Carbon::createFromFormat('m-Y', $month->month . '-' . $month->year);
                $bonusesSum += ObtainedBonus::onDay($user->id, $award_date->day($i)->format('Y-m-d'));

                //test bonuses
                $bonusesSum += $user->testBonuses->sum('amount');

            }
            $result[] = [
             'total' => $earningSum + $bonusesSum,
             'name' => $user->name,
             'last_name' => $user->last_name,
             'email' => $user->email,
             'id' => $user->id,
            ];
        }

        usort($result, function($a, $b) {
            return $a['total'] > $b['total'];
        });

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