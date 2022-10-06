<?php

namespace App\Service;

use App\Models\GroupUser;
use App\ProfileGroup;
use App\Service\Department\UserService;

class AnalyticService
{
    /**
     * @param ProfileGroup $group
     * @param $date
     * @return int
     */
    public function getFiredUsersPerMonth(ProfileGroup $group, $date): int
    {
        return GroupUser::withTrashed()->where('group_id', $group->id)
            ->whereYear('to', $date->year)->whereMonth('to', $date->month)->count();
    }

    public function getFiredUsersPerMonthPercent(ProfileGroup $group, $date)
    {
        $allUser = (new UserService())->getEmployees($group->id, $date->toDateString());

    }
}