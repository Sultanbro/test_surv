<?php

namespace App\Listeners;

use App\Events\TrackKpiItemEvent;
use App\Models\History;
use App\Models\Kpi\KpiItem;

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
     * @param TrackKpiItemEvent $event
     * @return void
     */
    public function handle(TrackKpiItemEvent $event): void
    {
        $data = $event->data;
        /** @var KpiItem $kpiItem */
        $kpiItem = KpiItem::withTrashed()->with('activity')->findOrFail($data['id']);
        $activity = $kpiItem->activity;

        History::query()->create([
            'reference_table' => 'App\Models\Kpi\KpiItem',
            'reference_id' => $kpiItem->getKey(),
            'actor_id' => auth()->id() ?? 5,
            'payload' => json_encode([
                'name' => $data['name'],
                'kpi_id' => $kpiItem->kpi_id,
                'activity_id' => $data['activity_id'],
                'plan' => $data['plan'],
                'daily_plan' => $activity?->daily_plan,
                'share' => $data['share'],
                'cell' => $data['cell'],
                'method' => $data['method'],
                'unit' => $data['unit'],
            ])
        ]);
    }
}
