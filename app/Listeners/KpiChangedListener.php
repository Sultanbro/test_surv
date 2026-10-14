<?php

namespace App\Listeners;

use App\CacheStorage\KpiItemsCacheStorage;
use App\Events\KpiChangedEvent;

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
     * @param KpiChangedEvent $event
     * @return void
     */
    public function handle(KpiChangedEvent $event): void
    {
        KpiItemsCacheStorage::onKpiChanged($event);
    }
}
