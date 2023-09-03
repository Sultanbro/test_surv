<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\ProfileGroup;
use App\User;

/**
* Класс для работы с Service.
*/
final class GetGroupsService
{
    /**
     * @return array[]
     */
    public function handle(): array
    {
        $groups = ProfileGroup::whereHasAnalytics()
            ->isActive()
            ->get();
        $user   = auth()->user() ?? User::findOrFail(5);

        if (!$user->isAdmin())
        {
            $groups = $groups->filter(function ($group) {
                $editorsId = json_decode($group->editors_id) ?? [];

                return in_array(auth()->id(), $editorsId);
            });
        }

        return [
            'groups' => [
                'is_active'     => $groups,
                'is_archived'   => ProfileGroup::isArchived()->get()
            ]
        ];
    }
}