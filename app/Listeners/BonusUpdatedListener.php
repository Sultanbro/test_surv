<?php

namespace App\Listeners;

use App\Events\BonusUpdated;
use App\Models\Kpi\Bonus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class BonusUpdatedListener
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
    public function handle(BonusUpdated $event)
    {
        $item = Bonus::query()->findOrFail($event->bonus_id);

        DB::table('histories')->insert([
            'reference_table'   => 'App\Models\Kpi\Bonus',
            'reference_id'      => $item->id,
            'actor_id'          => 5,
            'payload'           => json_encode([
                'title'  => $item->title ?? null,
                'sum' => $item->sum ?? null,
                'group_id'   => $item->group_id ?? null,
                'activity_id'   => $item->activity_id ?? null,
                'unit'        => $item->unit ?? null,
                'quantity'        => $item->quantity ?? null,
                'daypart'        => $item->daypart ?? null,
                'from'        => $item->from ?? null,
                'to'        => $item->to ?? null,
                'text'        => $item->text ?? null,
            ]),
            'created_at'         => now(),
            'updated_at'         => now()
        ]);
    }
}