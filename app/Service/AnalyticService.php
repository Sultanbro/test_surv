<?php

namespace App\Service;

use App\Models\Analytics\UserStat;
use App\Models\GroupUser;
use App\ProfileGroup;
use App\Repositories\ProfileGroupRepository;
use App\Service\Department\UserService;
use Carbon\Carbon;
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
        return round(($firedUsers/($allUsers + $firedUsers)) * 100, 1);
    }

    /**
     * @param $date
     * @param $groupId
     * @return array
     */
    public function userStatisticsPerMonth($date, $groupId): array
    {
        $carbon = $this->getDate($date);
        $users  = collect((new UserService)->getUsers($groupId, $carbon))->pluck('id')->toArray();
        $monthInYear = 12;
        $statistics = [];
        foreach ($users as $user)
        {
            for ($month = 1; $month <= $monthInYear; $month++)
            {
                $statistics[$user][$month]= UserStat::
                    select(DB::raw('SUM(value) as total'))
                    ->where('user_id', $user)
                    ->whereYear('date', $date['year'])
                    ->whereMonth('date', $month)
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
}