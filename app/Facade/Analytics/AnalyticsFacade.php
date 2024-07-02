<?php

namespace App\Facade\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\DTO\Analytics\V2\UtilityDto;
use App\Models\Analytics\Activity;
use Illuminate\Support\Facades\Facade;

/**
 * @method static utility(UtilityDto $utilityDto)
 * @method static rentability(UtilityDto $utilityDto)
 * @method static decompositionTable(GetAnalyticDto $dto)
 * @method static activitiesViews(int $groupId, array $array)
 * @method static analytics(GetAnalyticDto $dto)
 * @method static userStatisticFormTable(Activity $activity, string $date, int $groupId = null)
 */
class AnalyticsFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'top_values';
    }
}