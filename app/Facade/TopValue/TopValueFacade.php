<?php

namespace App\Facade\TopValue;

use Illuminate\Support\Facades\Facade;

/**
 * @method static utility(\App\DTO\Analytics\V2\UtilityDto $utilityDto)
 * @method static rentability(\App\DTO\Analytics\V2\UtilityDto $utilityDto)
 */
class TopValueFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'top_values';
    }
}