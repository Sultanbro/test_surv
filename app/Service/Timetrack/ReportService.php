<?php
declare(strict_types=1);

namespace App\Service\Timetrack;

use App\Helpers\UserHelper as Helper;
use App\Repositories\ProfileGroupRepository;
use App\Repositories\UserFineRepository;
use App\Repositories\UserRepository;
use App\Service\Department\UserService;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
final class ReportService
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

    /**
     * @Post {
     *  "groupId": 26,
     *  "year": 2022,
     *  "month": 2,
     *  "day": 1
     * }
     *
     * @param int $groupId
     * @param int $year
     * @param int $month
     * @param int $day
     * @return array
     */
    public function post(
        int $groupId,
        int $year,
        int $month,
        int $day
    )
    {
        $userIds = (new UserService)->getEmployeeIds($groupId, $this->getData($year, $month, $day)->format('Y-m-d'));
        $users   = $this->userRepository->userTimeTrackRelation($userIds, $year, $month);
        $data    = [];

        foreach ($users as $user)
        {
            $userFines = $this->userFineRepository->getUserFines($user->id, $year, $month);
            $days = $user->timetracking->pluck('date')->unique()->toArray();

            if (Helper::showFiredEmployee($user, $year, $month))
            {
                foreach ($days as $day) {
                    $data[$user->id][$day] = $user->timetracking->where('date', $day)->min('enter')->format('H:i');
                }

                $fines = [];
                for ($i = 1; $i <= $this->getData($year, $month, $day)->daysInMonth; $i++) {
                    $d = $i;
                    if(strlen ($i) == 1) $d = '0' . $i;

                    $x = $userFines->where('day', $d);
                    if($x->count() > 0) {
                        $fines[$i] = ['yes'];
                    } else {
                        $fines[$i] = [];
                    }
                }

                $data[$user->id]['fines']   = $fines;
                $data[$user->id]['name']    = $user->full_name;
                $data[$user->id]['user_id'] = $user->id;
            }
        }

        return $data;
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