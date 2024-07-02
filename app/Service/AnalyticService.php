<?php

namespace App\Service;

use App\Models\Analytics\Activity;
use App\Models\Analytics\UserStat;
use App\Models\GroupUser;
use App\ProfileGroup;
use App\Repositories\ProfileGroupRepository;
use App\Service\Department\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use App\User;

class AnalyticService
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * @param ProfileGroup $group
     * @param $date
     * @return int
     */
    public function getFiredUsersPerMonth(ProfileGroup $group, $date): int
    {
        return count($this->userService->getFiredEmployees($group->id, $date->toDateString()));
    }

    /**
     * @param ProfileGroup $group
     * @param $date
     * @return float
     */
    public function getFiredUsersPerMonthPercent(ProfileGroup $group, $date): float
    {
        $firedUsers = $this->getFiredUsersPerMonth($group, $date);
        $allUsers   = count($this->userService->getUsers($group->id, $date->toDateString()));
        
        
        return ($allUsers + $firedUsers) > 0
            ? round(($firedUsers/($allUsers + $firedUsers)) * 100, 1)
            : 0;
    }

    /**
     * @param $date
     * @param $groupId
     * @return array
     */
    public function userStatisticsPerMonth($date, $groupId, $activity_id): array
    {
        $carbon = $this->getDate($date);
        $users  = collect((new UserService)->getUsers($groupId, $carbon))->pluck('id')->toArray();
        $monthInYear = 12;
        $statistics = [];

        $necessaryFields = DB::raw($this->necessaryFields($activity_id));
        
        foreach ($users as $user)
        {
            for ($month = 1; $month <= $monthInYear; $month++)
            {
                $statistics[$user][$month]= UserStat::
                    select($necessaryFields)
                    ->where('user_id', $user)
                    ->whereYear('date', $date['year'])
                    ->whereMonth('date', $month)
                    ->where('activity_id', $activity_id)
                    ->groupByRaw('user_id, year(date), month(date)')
                    ->first() ?? 0;
            }
        }
        return [
            'statistics' => $statistics,
            'users' => (new UserService)->getUsers($groupId, $carbon)
        ];
    }

    /**
     * determine necessary field total or avg from Activity method
     * 
     * @return String
     */
    private function necessaryFields($activity_id): String
    {
        $activity = Activity::withTrashed()->find($activity_id);

        $method = $activity ? $activity->method : Activity::METHOD_SUM;

        return in_array($method, [
                Activity::METHOD_SUM,
                Activity::METHOD_SUM_NOT_LESS,
                Activity::METHOD_AVG_NOT_MORE,
            ])
            ? 'SUM(value) as total'
            : 'AVG(value) as total';
    }

    /**
     * @param $groupId
     * @return mixed
     */
    public function getProfileGroup($groupId)
    {
        return app(ProfileGroupRepository::class)->getGroup($groupId);
    }

    private function getDate($date)
    {
        $date = $date['year'] . '-' . $date['month'] . '-' . 1;

        return Carbon::parse($date)->endOfMonth()->format('Y-m-d');
    }

    /**
     * @param Activity $activity
     * @param string $dailyPlan
     * @param string $planUnit
     * @param int $year
     * @param string $month
     * @return void
     * @throws Exception
     */
    public function updatePlanPerMonth(
        Activity $activity,
        string $dailyPlan,
        string $planUnit,
        int $year,
        string $month
    )
    {
        try {
            $activity->plans()->updateOrCreate(
                [
                    'year' => $year,
                    'month' =>$month
                ],
                [
                    'plan' => $dailyPlan,
                    'plan_unit' => $planUnit
                ]
            );
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}