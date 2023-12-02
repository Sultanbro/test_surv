<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\ProfileGroup;

/**
* Класс для работы с Service.
*/
class GetPredictsService
{
    public function handle()
    {
        return ProfileGroup::isActive()->hasAnalytics()->get()
            ->map(function ($group) {

                return [
                    'id'    => $group->id,
                    'name'  => $group->name,
                    'users' => [
                        'total'     => $group->activeUsers->count(),
                        'trainees'  => $group->activeTrainees->count(),
                        'employees' => $group->activeEmployees->count()
                    ],
                    'plan'  => $group->required
                ];
            });
    }
}