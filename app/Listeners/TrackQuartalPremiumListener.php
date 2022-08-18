<?php

namespace App\Listeners;

use App\Events\TrackQuartalPremiumEvent;
use App\Models\QuartalPremium;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class TrackQuartalPremiumListener
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
    public function handle(TrackQuartalPremiumEvent $event)
    {
        $quartalPremium = QuartalPremium::query()->find($event->quartalPremiumId);

        DB::table('histories')->insert([
            'reference_table'   => 'App\Models\QuartalPremium',
            'reference_id'      => $quartalPremium->id,
            'actor_id'          => 5,
            'payload'           => json_encode([
                'activity_id'   => $quartalPremium->activity_id ?? null,
                'title'         => $quartalPremium->title ?? null,
                'text'          => $quartalPremium->text ?? null,
                'plan'          => $quartalPremium->plan ?? null,
                'from'          => $quartalPremium->from ?? null,
                'to'            => $quartalPremium->to ?? null
            ]),
            'created_at'         => now(),
            'updated_at'         => now()
        ]);
    }
}
