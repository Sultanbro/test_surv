<?php

namespace App\Facade\TopValue;

use App\CacheStorage\AnalyticCacheStorage;
use App\DTO\Analytics\V2\UtilityDto;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\ProfileGroup;
use App\Models\Analytics\TopValue as ValueModel;

class TopValue
{
    public function utility(
        UtilityDto $dto
    )
    {
        $groupIds   = $dto->groupIds ?? ProfileGroup::profileGroupsWithArchived($dto->year, $dto->month, false, false, ProfileGroup::SWITCH_UTILITY);
        $groups     = collect(AnalyticCacheStorage::get(AnalyticEnum::GROUP_KEY))->whereIn('id', $groupIds);
        $date       = DateHelper::firstOfMonth($dto->year, $dto->month);

        foreach ($groups as $group)
        {
            $tops = ValueModel::getByGroupAndDate($group->id, $date)->get()
                ->each(function ($top) {
                    $top->place       = 1;
                    $top->editable    = false;
                    $top->edit_value  = false;
                    $top->diff        = 0;
                    $top->key         = $top->id * 1000;
                });

        }
    }

}