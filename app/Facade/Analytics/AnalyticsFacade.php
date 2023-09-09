<?php

namespace App\Facade\Analytics;

use Illuminate\Support\Facades\Facade;

/**
 * @method static utility(\App\DTO\Analytics\V2\UtilityDto $utilityDto)
 * @method static rentability(\App\DTO\Analytics\V2\UtilityDto $utilityDto)
 * @method static decompositionTable(\App\DTO\Analytics\V2\GetAnalyticDto $dto)
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