<?php

namespace App\Listeners;

use App\Events\TrackKpiItemEvent;
use App\Models\History;
use App\Models\Kpi\KpiItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TrackKpiItemListener
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
    public function handle(TrackKpiItemEvent $event)
    {
        $kpiItem = KpiItem::query()->withTrashed()->findOrFail($event->kpiItemId);


        History::query()->create([
            'reference_table'   => 'App\Models\Kpi\KpiItem',
            'reference_id'      => $kpiItem->id,
            'actor_id'          => auth()->id() ?? 5,
            'payload'           => json_encode([
                'name'          => $kpiItem->name,
                'kpi_id'        => $kpiItem->kpi_id,
                'activity_id'   => $kpiItem->activity_id,
                'plan'          => $kpiItem->plan,
                'daily_plan'    => $kpiItem->activity->daily_plan,
                'share'         => $kpiItem->share,
                'cell'          => $kpiItem->cell,
                'method'        => $kpiItem->method,
                'unit'          => $kpiItem->unit,
            ])
        ]);
    }
}
