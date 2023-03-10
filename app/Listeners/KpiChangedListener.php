<?php

namespace App\Listeners;

use App\Events\KpiChangedEvent;
use App\Helpers\KpiItemsCacheHelper;

class KpiChangedListener
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
     * @param  \App\Events\KpiChangedEvent  $event
     * @return void
     */
    public function handle(KpiChangedEvent $event)
    {
        KpiItemsCacheHelper::onKpiChanged($event);
    }
}
