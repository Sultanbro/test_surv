<?php

namespace App\Listeners;

use App\Events\TrackGroupChangingEvent;
use App\Models\History;
use App\ProfileGroup;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrackGroupChangingListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(TrackGroupChangingEvent $event)
    {
        $user  = User::query()->findOrFail($event->userId);

        History::query()->create([
            'reference_table' => get_class($user),
            'reference_id'    => $event->userId,
            'actor_id'        => 1,
            'payload'         => json_encode([
                'action'    => $event->action,
                'salary'    => $user->zarplata->zarplata,
                'group_id'   => $event->groupId,
                'date' => now()->toDateString(),
            ])
        ]);
    }
}
