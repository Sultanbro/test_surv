<?php

namespace App\Service\Awards\AwardTypes;

use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Models\Award\Award;
use App\Models\Course;
use App\Models\CourseResult;
use App\Position;
use App\ProfileGroup;
use App\Salary;
use App\Service\Department\UserService;
use App\Service\Interfaces\Award\AwardInterface;
use App\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AccrualAwardService implements AwardInterface
{

    public function fetch(array $data): array
    {

    }

    public function store(StoreAwardRequest $request)
    {

    }

    public function update(UpdateAwardRequest $request)
    {

    }

    /**
     * @param $user
     * @return array
     */
    public function getAccrual($user, $award_type_id): array
    {

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