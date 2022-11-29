<?php

namespace App\Service\Awards\AwardTypes;

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
                throw new BusinessLogicException('targetable_type, targetable_id are required for certificate award');
            }
            if (Award::query()
                ->where('award_category_id', $request->input('award_category_id'))->exists()) {
                throw new BusinessLogicException('accrual category already has award');
            }

            $params = [
                'award_category_id' => $request->input('award_category_id'),
                'targetable_type'      => $request->input('targetable_type'),
                'targetable_id'      => $request->input('targetable_id'),
            ];

            $success = Award::query()->create($params);

            return $success;
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
        $groups = $user->groups->pluck('id')->toArray();

        $today = Carbon::now();
        $date = Carbon::createFromDate($today->year, $today->month, 1);

        $awardCategories = AwardCategory::query()
            ->where('type', $type)
            ->with('awards',function ($query) use ($user, $groups) {
                $query->where(function ($q) use ($user) {
                    $q->where('targetable_id',$user->position_id)
                        ->where('targetable_type', self::POSITION);
                })
                    ->orWhere(function ($q) use ($groups) {
                        $q->whereIn('targetable_id', $groups)
                            ->where('targetable_type', self::GROUP);
                    });

            })
            ->get();

        foreach ($awardCategories as $awardCategory){
            $award = $awardCategory['awards'][0];
            $targetable_type = $award['targetable_type'];
            $targetable_id = $award['targetable_id'];

            if ($targetable_type == self::GROUP){
                $user_ids = collect( (new UserService)
                    ->getEmployees($targetable_id, $date->format('Y-m-d')))
                    ->pluck('id')->toArray();
                $result[] = [
                    'name' => $awardCategory->name,
                    'description'=>$awardCategory->description,
                    'top' => $this->getTopSalaryEmployees($user_ids, $date, $targetable_id),
                ];
            }

            if ($targetable_type == self::POSITION){
                $user_ids = Position::query()
                    ->findOrFail($targetable_id)
                    ->users
                    ->pluck('id');
                $result[] = [
                    'name' => $awardCategory->name,
                    'description'=>$awardCategory->description,
                    'top' => $this->getTopSalaryEmployees($user_ids, $date, $groups[0]),

                ];

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


}