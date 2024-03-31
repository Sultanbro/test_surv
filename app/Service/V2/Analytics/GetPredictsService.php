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
            ->select(DB::raw('group_user.id as group_id'))
            ->select(DB::raw('count(*) as count'))
            ->join(DB::raw('user_description as ud'), 'ud.user_id', '=', 'users.id')
            ->join(DB::raw('group_user as piv'), 'piv.user_id', '=', 'users.id')
            ->where('piv.status', 'active')
            ->where('users.created_at', '>=', $from)
            ->groupBy('group_id');

        $activeUsersSubQuery = $baseSubQuery;

        $activeTraineeSubQuery = $baseSubQuery->where('ud.is_trainee', 0);
        $activeEmployeeSubQuery = $baseSubQuery->where('ud.is_trainee', 1);

        return ProfileGroup::isActive()
            ->leftJoinSub($activeUsersSubQuery, 'active_users', 'profile_groups.id', 'active_users.group_id')
            ->leftJoinSub($activeTraineeSubQuery, 'active_trainees', 'profile_groups.id', 'active_trainees.group_id')
            ->leftJoinSub($activeEmployeeSubQuery, 'active_employees', 'profile_groups.id', 'active_employees.group_id')
            ->addSelect(DB::raw('active_users.count as users_total'))
            ->addSelect(DB::raw('active_trainees.count as users_trainees'))
            ->addSelect(DB::raw('active_employees.count as users_employees'))
            ->hasAnalytics()
            ->get()
            ->map(function ($group) {
                dd($group);
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'users' => [
                        'total' => $group->activeUsers->count(),
                        'trainees' => $group->activeTrainees->count(),
                        'employees' => $group->activeEmployees->count()
                    ],
                    'plan' => $group->required
                ];
            });
    }
}