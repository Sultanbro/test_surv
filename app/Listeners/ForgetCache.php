<?php

namespace App\Listeners;

use App\Events\KpiChanged;
use App\Helpers\KpiItemsCacheHelper;

class ForgetCache
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\KpiChanged  $event
     * @return void
     */
    public function handle(KpiChanged $event)
    {
        KpiItemsCacheHelper::flush();
    }
}
