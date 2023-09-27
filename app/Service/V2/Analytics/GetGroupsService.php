<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\ProfileGroup;
use App\Traits\AnalyticTrait;
use App\User;

/**
* Класс для работы с Service.
*/
final class GetGroupsService
{
    use AnalyticTrait;
    /**
     * @return array[]
     */
    public function handle(): array
    {
        $groups = $this->groups()
            ->whereIn('has_analytics', [ProfileGroup::HAS_ANALYTICS, ProfileGroup::NOT_ANALYTICS])
            ->where('active', ProfileGroup::IS_ACTIVE);

        $user   = auth()->user() ?? User::findOrFail(18);

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
                'is_archived'   => $this->groups()->where('has_analytics', ProfileGroup::ARCHIVED)
            ]
        ];
    }
}