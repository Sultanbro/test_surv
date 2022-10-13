<?php

namespace App\Service;

use App\Models\GroupUser;
use App\ProfileGroup;
use App\Service\Department\UserService;

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
}