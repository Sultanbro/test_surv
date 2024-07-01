<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class MailingFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mailing';
    }
}