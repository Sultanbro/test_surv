<?php

namespace Eddir\Messenger\Facades;

use Illuminate\Support\Facades\Facade;

class MessengerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'messenger';
    }
}
