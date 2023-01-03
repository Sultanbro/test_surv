<?php

namespace App\Listeners\TimeTrack;

use App\Events\TimeTrack\CreateTimeTrackHistoryEvent;
use App\Repositories\TimeTrackHistoryRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateTimeTrackHistoryListener
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
     * @param  CreateTimeTrackHistoryEvent  $event
     * @return void
     */
    public function handle(CreateTimeTrackHistoryEvent $event)
    {
        (new TimeTrackHistoryRepository)->createHistory(
            $event->userId,
            'Принятие на работу без стажировки',
            date('Y-m-d')
        );
    }
}
