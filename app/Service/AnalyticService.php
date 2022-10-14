<?php

namespace App\Service;

use App\Models\Analytics\UserStat;
use App\Models\GroupUser;
use App\ProfileGroup;
use App\Repositories\ProfileGroupRepository;
use App\Service\Department\UserService;
use Illuminate\Support\Facades\DB;

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
     * @param $year
     * @param $groupId
     * @return array
     */
    public function userStatisticsPerMonth($year, $groupId): array
    {
        $users = $this->getProfileGroup($groupId)->users()->pluck('id')->toArray();
        $monthInYear = 12;
        $statistics = [];
        for ($month = 1; $month <= $monthInYear; $month++)
        {
            $statistics[$month] = UserStat::query()
                ->with('users', fn ($user) => $user->select('id', 'name', 'last_name'))
                ->select(DB::raw('SUM(value) as total'),'user_id')
                ->whereIn('user_id', $users)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->groupByRaw('user_id, year(date), month(date)')
                ->get();
        }

        return $statistics;
    }

    /**
     * @param $groupId
     * @return mixed
     */
    public function getProfileGroup($groupId)
    {
        return app(ProfileGroupRepository::class)->getGroup($groupId);
    }
}