<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\ProfileGroup;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Класс для работы с Service.
 */
class GetPredictsService
{
    public function handle()
    {
        $from = now()->firstOfMonth();

        $activeUsersSubQuery = $this->baseSubQuery($from);
        $activeTraineeSubQuery = $this->baseSubQuery($from)
            ->where('ud.is_trainee', 1);
        $activeEmployeeSubQuery = $this->baseSubQuery($from)
            ->where('ud.is_trainee', 0);

        return ProfileGroup::isActive()
            ->hasAnalytics()
            ->leftJoinSub($activeUsersSubQuery, 'active_users', 'active_users.group_id', 'profile_groups.id')
            ->leftJoinSub($activeTraineeSubQuery, 'active_trainees', 'active_trainees.group_id', 'profile_groups.id')
            ->leftJoinSub($activeEmployeeSubQuery, 'active_employees', 'active_employees.group_id', 'profile_groups.id')
            ->select([
                'id',
                'name',
                'required',
                DB::raw('count(active_users.user_id) as users_total'),
                DB::raw('count(active_trainees.user_id) as trainees_total'),
                DB::raw('count(active_employees.user_id) as employees_total')
            ])
            ->groupBy([
                'id',
                'name',
                'required'
            ])
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'users' => [
                        'total' => $group->users_total,
                        'trainees' => $group->trainees_total,
                        'employees' => $group->employees_total
                    ],
                    'plan' => $group->required
                ];
            });
    }

    private function baseSubQuery(Carbon $from): Builder
    {
        return DB::table('users')
            ->select([
                DB::raw('piv.group_id as group_id'),
                DB::raw('piv.user_id as user_id'),
            ])
            ->join(DB::raw('user_descriptions as ud'), 'ud.user_id', '=', 'users.id')
            ->join(DB::raw('group_user as piv'), 'piv.user_id', '=', 'users.id')
            ->where('piv.status', 'active')
            ->where('piv.from', '>=', $from);
    }
}