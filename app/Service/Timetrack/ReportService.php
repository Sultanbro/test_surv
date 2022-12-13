<?php

namespace App\Service\Timetrack;

use App\Repositories\ProfileGroupRepository;
use App\Repositories\UserFineRepository;
use App\Repositories\UserRepository;
use App\Service\Department\UserService;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
class ReportService
{
    public function __construct(
        public ProfileGroupRepository $profileGroupRepository,
        public UserRepository $userRepository,
        public UserFineRepository $userFineRepository
    )
    {
    }

    /**
     * @param $user
     * @return array
     */
    public function get($user): array
    {
        $groups = $this->profileGroupRepository->getActive();

        if (!$user->is_admin)
        {
            $groups = $this->profileGroupRepository->checkEditor($user->id);
        }

        return [
            'groups' => $groups,
            'years'  => [Carbon::now()->subYear(2)->year, Carbon::now()->subYear(1)->year, Carbon::now()->year]
        ];
    }

    public function post(
        int $groupId,
        int $year,
        int $month,
        int $day
    )
    {
        $group = $this->profileGroupRepository->getGroup($groupId);
        $userIds = (new UserService)->getEmployeeIds($groupId, $this->getData($year, $month, $day)->format('Y-m-d'));
        $users = $this->userRepository->userTimeTrackRelation($userIds, $year, $month);

        foreach ($users as $user)
        {
            $fines = $this->userFineRepository->getUserFines($user->id, $year, $month);
            $days = $user->timetracking->pluck('date')->unique()->toArray();
            dd( $days);
        }

        dd('here');
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @return Carbon
     */
    private function getData($year, $month, $day): Carbon
    {
        return Carbon::createFromDate($year, $month, $day);
    }
}