<?php

namespace App\Providers;

use App\Events\UserStatUpdatedEvent;
use App\Events\EmailNotificationEvent;
use App\Events\TimeTrack\CreateTimeTrackHistoryEvent;
use App\Events\TrackCourseItemFinishedEvent;
use App\Events\TrackGroupChangingEvent;
use App\Events\TrackUserFiredEvent;
use App\Events\TransferUserInGroupEvent;
use App\Events\WorkdayEvent;
use App\Listeners\UserStatUpdatedListener;
use App\Listeners\EmailNotificationListener;
use App\Listeners\TimeTrack\CreateTimeTrackHistoryListener;
use App\Listeners\TrackCourseItemFinishedListener;
use App\Listeners\TrackGroupChangingListener;
use App\Listeners\TrackUserFiredListener;
use App\Listeners\TransferUserInGroupListener;
use App\Listeners\WorkdayListener;
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
        \App\Events\KpiChangedEvent::class => [
            \App\Listeners\KpiChangedListener::class
        ],
        UserStatUpdatedEvent::class => [
            UserStatUpdatedListener::class
        ],
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
