<?php

namespace App\Providers;

use App\Events\TrackKpiItemEvent;
use App\Events\TrackKpiUpdatesEvent;
use App\Events\TrackQuartalPremiumEvent;
use App\Listeners\TrackKpiItemListener;
use App\Listeners\TrackKpiUpdatesListener;
use App\Listeners\TrackQuartalPremiumListener;
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
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        TrackKpiUpdatesEvent::class => [
            TrackKpiUpdatesListener::class
        ],
        TrackQuartalPremiumEvent::class => [
            TrackQuartalPremiumListener::class
        ],
        'App\Events\BonusUpdated' => [
            'App\Listeners\BonusUpdatedListener'
        ],
        TrackKpiItemEvent::class => [
            TrackKpiItemListener::class
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
