<?php

namespace App\Listeners;

use App\Events\TrackUserFiredEvent;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class TrackUserFiredListener
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
     * @param  TrackUserFiredEvent  $event
     * @return void
     */
    public function handle(TrackUserFiredEvent $event): void
    {
        $user   = $event->user;
        $groups = $this->getGroupsId($user);

        $this->changeUserStatus($user);

        DB::table('histories')->insert([
            'reference_table'   => get_class($user),
            'reference_id'      => $user->id,
            'actor_id'          => 5,
            'payload'           => json_encode([
                'fired_date'    => now(),
                'groups'        => json_encode($groups)
            ])
        ]);
    }

    /**
     * @param $user
     * @return array
     */
    private function getGroupsId($user): array
    {
        return array_unique($user->groups()->pluck('name', 'id')->toArray());
    }

    /**
     * @param $user
     * @return void
     */
    private function changeUserStatus($user): void
    {
        $user->groups()->update([
            'to' => Carbon::now()->toDateTimeString(),
            'status' => 'fired'
        ]);
    }
}
