<?php

namespace App\Facade\TopValue;

use Illuminate\Support\Facades\Facade;

/**
 * @method static utility()
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