<?php

namespace App\Listeners;

use App\Events\UserUpdatedEvent;
use App\Models\History;
use App\User;
use Illuminate\Support\Facades\DB;

class UserUpdatedListener
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
     * @param UserUpdatedEvent $event
     * @return void
     */
    public function handle(UserUpdatedEvent $event)
    {
        /** @var User $user */
        $user = User::withTrashed()->findOrFail($event->id);

        DB::table('histories')->insert([
            'reference_table'   => 'App\User',
            'reference_id'      => $user->id,
            'actor_id'          => 5,
            'payload'           => json_encode([
                'position_id'   => $user->position_id ?? 0,
                'work_chart_id' => $user->work_chart_id
            ]),
            'type'              => History::USER_PROFILE_CHANGED,
            'created_at'        => now(),
            'updated_at'        => now(),

        ]);
    }
}
