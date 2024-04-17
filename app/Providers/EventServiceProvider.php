<?php

namespace App\Providers;

use App\Events\{ActivityUpdated,
    BonusUpdated,
    KpiChangedEvent,
    TrackKpiItemEvent,
    TrackKpiUpdatesEvent,
    TrackQuartalPremiumEvent,
    TrackTaxGroupItemEvent,
    UserStatUpdatedEvent,
    EmailNotificationEvent,
    TrackUserFiredEvent,
    TimeTrack\CreateTimeTrackHistoryEvent,
    TrackCourseItemFinishedEvent,
    TrackGroupChangingEvent,
    TransferUserInGroupEvent,
    UserUpdatedEvent,
    WorkdayEvent};
use App\Listeners\{ActivityUpdatedListener,
    BonusUpdatedListener,
    EventListener,
    KpiChangedListener,
    TrackKpiItemListener,
    TrackKpiUpdatesListener,
    TrackQuartalPremiumListener,
    TrackTaxGroupItemListener,
    UserStatUpdatedListener,
    EmailNotificationListener,
    TimeTrack\CreateTimeTrackHistoryListener,
    TrackCourseItemFinishedListener,
    TrackGroupChangingListener,
    TrackUserFiredListener,
    TransferUserInGroupListener,
    UserUpdatedListener,
    WorkdayListener};

use App\Models\WorkChart\WorkChartModel;
use App\Observers\Timetracking\TimetrackingObserver;
use App\Observers\UserObserver;
use App\Observers\WorkChart\WorkChartObserver;
use App\Timetracking;
use App\User;
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
            EventListener::class,
        ],
        TrackKpiUpdatesEvent::class => [
            TrackKpiUpdatesListener::class
        ],
        TrackQuartalPremiumEvent::class => [
            TrackQuartalPremiumListener::class
        ],
        BonusUpdated::class => [
            BonusUpdatedListener::class
        ],
        TrackKpiItemEvent::class => [
            TrackKpiItemListener::class
        ],
        ActivityUpdated::class => [
            ActivityUpdatedListener::class
        ],
        TrackTaxGroupItemEvent::class => [
            TrackTaxGroupItemListener::class
        ],
        TrackUserFiredEvent::class => [
            TrackUserFiredListener::class
        ],
        TrackGroupChangingEvent::class => [
            TrackGroupChangingListener::class
        ],
        TransferUserInGroupEvent::class => [
            TransferUserInGroupListener::class
        ],
        TrackCourseItemFinishedEvent::class => [
            TrackCourseItemFinishedListener::class
        ],
        CreateTimeTrackHistoryEvent::class => [
            CreateTimeTrackHistoryListener::class
        ],
        EmailNotificationEvent::class => [
            EmailNotificationListener::class
        ],
        WorkdayEvent::class => [
            WorkdayListener::class
        ],
        KpiChangedEvent::class => [
            KpiChangedListener::class
        ],
        UserStatUpdatedEvent::class => [
            UserStatUpdatedListener::class
        ],

        UserUpdatedEvent::class => [
            UserUpdatedListener::class
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
        User::observe(UserObserver::class);
        Timetracking::observe(TimetrackingObserver::class);
        WorkChartModel::observe(WorkChartObserver::class);
    }
}
