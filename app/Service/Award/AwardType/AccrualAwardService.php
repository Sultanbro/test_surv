<?php

namespace App\Service\Award\AwardType;

use App\Enums\AwardTypeEnum;
use App\Exceptions\News\BusinessLogicException;
use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Models\Course;
use App\Models\CourseResult;
use App\Position;
use App\ProfileGroup;
use App\Salary;
use App\Service\Department\UserService;
use App\Service\Interfaces\Award\AwardInterface;
use App\User;
use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class AccrualAwardService implements AwardInterface
{
    const POSITION = 'App\Position';
    const GROUP = 'App\ProfileGroup';

    public function fetch(array $params): array
    {
        $user = User::query()->findOrFail($params['user_id']);
        try {
            $type = AwardTypeEnum::TYPES[$params['key']];

            return $this->getAccrual($user, $type);

        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function store(StoreAwardRequest $request)
    {
        try {
            if (!($request->has('targetable_type') &&
                $request->has('targetable_id'))) {
                return \response()->success('targetable_type, targetable_id обязательны к заполнению', 500, 'error');
            }
            if (Award::query()
                ->where('award_category_id', $request->input('award_category_id'))->exists()) {
                return \response()->success('Категория начисления уже имеет награду', 500, 'error');
            }

           if (Award::query()
               ->where('targetable_id',  $request->input('targetable_id'))
               ->where('targetable_type', $request->input('targetable_type'))->exists()) {
                return \response()->success('На данный отдел или позицию уже существует награда, пожалуйста выберите другой отдел, позицию', 500, 'error');
            }

            $params = [
                'award_category_id' => $request->input('award_category_id'),
                'targetable_type'      => $request->input('targetable_type'),
                'targetable_id'      => $request->input('targetable_id'),
            ];

            $success = Award::query()->create($params);

            return \response()->success($success);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function update(UpdateAwardRequest $request, Award $award)
    {
        try {
            $parameters = $request->except(['_method']);

            return $award->update($parameters);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $user
     * @return array
     */
    public function getAccrual($user, $type): array
    {
        $result = [];
        $group = $user->inGroups()->first();
        if (!$group){
            $group = $user->inGroups(true)->first();
        }

        $group_id = $group->id;
        $today = Carbon::now();
        $date = Carbon::createFromDate($today->year, $today->month, 1);


        $awardCategories = AwardCategory::query()
            ->where('type', $type)
            ->withWhereHas('awards',function ($query) use ($user, $group_id) {
                $query->where( function ($q) use ($user, $group_id){
                    $q->orWhere(function ($qu) use ($user) {
                        $qu->where('targetable_id',$user->position_id)
                            ->where('targetable_type', self::POSITION);
                    })
                        ->orWhere(function ($qu) use ($group_id) {
                            $qu->where('targetable_id', $group_id)
                                ->where('targetable_type', self::GROUP);
                        });
                });

            })
            ->get();

        $read = false;
        foreach ($awardCategories as $awardCategory){
            $award = $awardCategory['awards'][0];
            $targetable_type = $award['targetable_type'];
            $targetable_id = $award['targetable_id'];
            $read = $read ? $read : $awardCategory['awards']->contains(fn($a) => $a->read);

            if ($targetable_type == self::GROUP){
                $user_ids = collect( (new UserService)
                    ->getEmployees($targetable_id, $date->format('Y-m-d')))
                    ->pluck('id')->toArray();
                $result[] = [
                    'name' => $awardCategory->name,
                    'description'=>$awardCategory->description,
                    'top' => $this->getTopSalaryEmployees($user_ids, $date),
                ];
            }

            if ($targetable_type == self::POSITION && $user->position_id == $targetable_id){
                $user_ids = User::whereHas('description', function ($query) use ($targetable_id) {
                    $query->where('position_id', $targetable_id)
                    ->where('is_trainee',0);
                })
                    ->get()
                    ->pluck('id');
                $result[] = [
                    'name' => $awardCategory->name,
                    'description'=>$awardCategory->description,
                    'top' => $this->getTopSalaryEmployees($user_ids, $date),

                ];

            }
        }

        return ['data' => $result, 'read' => $read];
    }

    public function getTopSalaryEmployees($user_ids,Carbon $date){
        $result = [];
        $month = $date->startOfMonth();

        $users = Salary::getUsersData($month, $user_ids);

        $internship_pay_rate = 0;
        foreach ($users as $user){

            $group = $user->inGroups()->first();
            if (!$group){
                $group = $user->inGroups(true)->first();
            }
            $userFot = $user->calculateFot($internship_pay_rate, $date);
            $result[] = [
                'kpi' => $userFot['kpi'],
                'earnings' => $userFot['earnings'],
                'bonuses' => $userFot['bonuses'],
                'total' => array_sum(array_values($userFot)),
                'position' => $user->position?->position,
                'group' => $group->name ?? '',
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


}