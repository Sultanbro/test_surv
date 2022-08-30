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
     * @param  object  $event
     * @return void
     */
    public function handle(TrackGroupChangingEvent $event)
    {
        $this->updateOrCreate($event);
    }

    private function updateOrCreate($event)
    {
        switch ($event) {
            case $event->action == self::ACTION_ADD:
                return $this->creating($event);
            case $event->action == self::ACTION_DELETE:
                return $this->updating($event);
        }
    }

    private function creating(TrackGroupChangingEvent $event)
    {
        $user = User::query()->findOrFail($event->userId);
        $this->updateDeletedAt($event);

        return History::query()->create([
            'reference_table'   => 'App\User',
            'reference_id'      => $event->userId,
            'actor_id'          => 1,
            'payload'           => json_encode([
                'add_action_date'       => Carbon::now()->toDateString(),
                'delete_action_date'    => null,
                'group_id'              => $event->groupId,
                'salary'                => $user->zarplata->zarplata
            ])
        ]);
    }

    private function updating($event)
    {
        $original = History::query()->where([
            ['reference_table', 'App\User'],
            ['reference_id', $event->userId],
        ])->where([
            ['payload->group_id', $event->groupId]
        ]);

        $payload = json_decode($original->first()->getOriginal()['payload'], true);
        $payload['delete_action_date'] = '2022-09-23';

        return $original->latest()->update([
            'payload' => $payload
        ]);
    }

    private function updateDeletedAt($event)
    {
        GroupUser::withTrashed()->where([
            ['user_id', '=', $event->userId],
            ['group_id', '=', $event->groupId]
        ])->whereNotNull('deleted_at')->update([
            'deleted_at' => null
        ]);;
    }
}
