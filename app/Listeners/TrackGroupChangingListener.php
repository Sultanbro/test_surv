<?php

namespace App\Listeners;

use App\Events\TrackGroupChangingEvent;
use App\Models\GroupUser;
use App\Models\History;
use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrackGroupChangingListener
{
    const ACTION_ADD = 'add';

    const ACTION_DELETE = 'delete';

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
     * @param  TrackGroupChangingEvent  $event
     * @return void
     */
    public function handle(TrackGroupChangingEvent $event)
    {
        $group = ProfileGroup::query()->findOrFail($event->groupId);
        $user  = User::query()->findOrFail($event->userId);

        History::query()->create([
            'reference_table'   => 'App\User',
            'reference_id'      => $event->userId,
            'actor_id'          => auth()->id() ?? 1,
            'payload'           => json_encode([
                'date'                  => Carbon::now()->toDateString(),
                'action'                => 'delete',
                'working_hours'         => $group->work_start . '-' . $group->work_end,
                'workdays'              => $group->workdays,
                'group_id'              => $event->groupId,
                'salary'                => $user->salaries()->orderBy('id', 'DESC')->first()->amount ?? null,
                'operating-mode'        => $user->full_time == 1 ? 'Full time' : 'Part time'
            ])
        ]);
    }
}
