<?php

namespace App\Providers;

use App\Events\TrackGroupChangingEvent;
use App\Events\TrackUserFiredEvent;
use App\Events\TransferUserInGroupEvent;
use App\Listeners\TrackGroupChangingListener;
use App\Listeners\TrackUserFiredListener;
use App\Listeners\TransferUserInGroupListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\Event::class => [
            \App\Listeners\EventListener::class,
        ],
        \App\Events\TrackKpiUpdatesEvent::class => [
            \App\Listeners\TrackKpiUpdatesListener::class
        ],
        \App\Events\TrackQuartalPremiumEvent::class => [
            \App\Listeners\TrackQuartalPremiumListener::class
        ],
        \App\Events\BonusUpdated::class => [
            \App\Listeners\BonusUpdatedListener::class
        ],
        \App\Events\TrackKpiItemEvent::class => [
            \App\Listeners\TrackKpiItemListener::class
        ],
        \App\Events\ActivityUpdated::class => [
            \App\Listeners\ActivityUpdatedListener::class
        ],
        TrackUserFiredEvent::class => [
            TrackUserFiredListener::class
        ],
        TrackGroupChangingEvent::class => [
            TrackGroupChangingListener::class
        ],
        TransferUserInGroupEvent::class => [
            TransferUserInGroupListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
