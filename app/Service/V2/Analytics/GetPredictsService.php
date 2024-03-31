<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\ProfileGroup;
use DB;

/**
 * Класс для работы с Service.
 */
class GetPredictsService
{
    public function handle()
    {
        $from = now()->firstOfMonth();
        $baseSubQuery = DB::table('users')
            ->select([
                DB::raw('piv.group_id as group_id'),
                DB::raw('piv.user_id as user_id'),
            ])
            ->join(DB::raw('user_descriptions as ud'), 'ud.user_id', '=', 'users.id')
            ->join(DB::raw('group_user as piv'), 'piv.user_id', '=', 'users.id')
            ->where('piv.status', 'active');
//            ->where('users.created_at', '>=', $from);

        $activeUsersSubQuery = $baseSubQuery;

        $activeTraineeSubQuery = $baseSubQuery->where('ud.is_trainee', 0);
        $activeEmployeeSubQuery = $baseSubQuery->where('ud.is_trainee', 1);

        dd(
            $activeUsersSubQuery->count(),
            $activeTraineeSubQuery->count(),
            $activeEmployeeSubQuery->count()
        );

        return ProfileGroup::isActive()
            ->leftJoinSub($activeUsersSubQuery, 'active_users', 'profile_groups.id', 'active_users.group_id')
            ->leftJoinSub($activeTraineeSubQuery, 'active_trainees', 'profile_groups.id', 'active_trainees.group_id')
            ->leftJoinSub($activeEmployeeSubQuery, 'active_employees', 'profile_groups.id', 'active_employees.group_id')
            ->select([
                'id',
                'name',
                'required',
                DB::raw('count(active_users.user_id) as users_total'),
                DB::raw('count(active_trainees.user_id) as users_trainees'),
                DB::raw('count(active_employees.user_id) as users_employees')
            ])
            ->hasAnalytics()
            ->groupBy([
                'id',
                'name',
                'required',
            ])
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'users' => [
                        'total' => $group->users_total,
                        'trainees' => $group->users_trainees,
                        'employees' => $group->users_employees
                    ],
                    'plan' => $group->required
                ];
            });
    }
}