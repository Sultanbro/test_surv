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
    public function handle2()
    {
        $from = now()->firstOfMonth();

        return ProfileGroup::query()
            ->select(
                'profile_groups.id',
                'profile_groups.name',
                'profile_groups.required',
                DB::raw('count(users.id) as users_total'),
                DB::raw('sum(case when ud.is_trainee = 0 then 1 else 0 end) as trainees_total'),
                DB::raw('sum(case when ud.is_trainee = 1 then 1 else 0 end) as employees_total')
            )
            ->join('group_user as piv', 'piv.group_id', '=', 'profile_groups.id')
            ->join('users', 'piv.user_id', '=', 'users.id')
            ->join('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('piv.status', 'active')
            ->where('profile_groups.active', ProfileGroup::IS_ACTIVE)
            ->whereIn('profile_groups.has_analytics', [ProfileGroup::HAS_ANALYTICS, ProfileGroup::ARCHIVED])
            ->where('piv.from', '>=', $from)
            ->groupBy('profile_groups.id', 'profile_groups.name', 'profile_groups.required')
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

    public function handle()
    {
        $date = now()->firstOfMonth();

        return ProfileGroup::query()
            ->select('id', 'name', 'required')
            ->withCount([
                'users as employees_count' => function ($query) use ($date) {
                    $query->where('group_user.status', 'active')
                        ->whereHas('description', function ($q) use ($date) {
                            $q->where('is_trainee', 0);
                            $q->where('applied', '>=', $date);
                        });
                },
                'users as trainees_count' => function ($query) use ($date) {
                    $query->where('group_user.status', 'active')
                        ->whereHas('description', function ($q) use ($date) {
                            $q->where('is_trainee', 1);
                            $q->where('applied', '>=', $date);
                        });
                }
            ])
            ->where('profile_groups.active', ProfileGroup::IS_ACTIVE)
            ->whereIn('profile_groups.has_analytics', [ProfileGroup::HAS_ANALYTICS, ProfileGroup::ARCHIVED])
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'users' => [
                        'total' => $group->employees_count + $group->trainees_count,
                        'trainees' => $group->trainees_count,
                        'employees' => $group->employees_count
                    ],
                    'plan' => $group->required
                ];
            });
    }
}
