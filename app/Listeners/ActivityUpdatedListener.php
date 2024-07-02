<?php

namespace App\Listeners;

use App\Events\ActivityUpdated;
use App\Models\Analytics\Activity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ActivityUpdatedListener
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
     * Событие при изменений данных в kpis мы храним историю.
     *
     * @param  TrackKpiUpdatesEvent  $event
     * @return void
     */
    public function handle(ActivityUpdated $event)
    {
        $item = Activity::query()->findOrFail($event->activity_id);

        DB::table('histories')->insert([
            'reference_table'   => 'App\Models\Analytics\Activity',
            'reference_id'      => $item->id,
            'actor_id'          => auth()->id() ?? 5,
            'payload'           => json_encode([
                'name'  => $item->name ?? null,
                'group_id'   => $item->group_id ?? null,
                'unit'        => $item->unit ?? null,
                'share' => $item->share ?? null,
                'daily_plan'   => $item->daily_plan ?? null,
                'method'        => $item->method ?? null,
                'view'        => $item->view ?? null,
                'source'        => $item->source ?? null,
                'weekdays'        => $item->source ?? null,
            ]),
            'created_at'         => now(),
            'updated_at'         => now()
        ]);

    }
}