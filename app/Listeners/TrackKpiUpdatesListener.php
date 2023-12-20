<?php

namespace App\Listeners;

use App\Events\TrackKpiUpdatesEvent;
use App\Models\Kpi\Kpi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class TrackKpiUpdatesListener
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
    public function handle(TrackKpiUpdatesEvent $event)
    {
        $kpi = Kpi::query()->withTrashed()->findOrFail($event->kpiId);

        DB::table('histories')->insert([
            'reference_table'   => 'App\Models\Kpi\Kpi',
            'reference_id'      => $kpi->id,
            'actor_id'          => 5,
            'payload'           => json_encode([
                'completed_80'  => $kpi->completed_80 ?? null,
                'completed_100' => $kpi->completed_100 ?? null,
                'lower_limit'   => $kpi->lower_limit ?? null,
                'upper_limit'   => $kpi->upper_limit ?? null,
                'colors'        => $kpi->colors ?? null,
                'children'      => $kpi->children,
                'off_limit'     => $kpi->off_limit
            ]),
            'created_at'         => now(),
            'updated_at'         => now(),

        ]);
    }
}
