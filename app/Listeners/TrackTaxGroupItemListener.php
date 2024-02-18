<?php

namespace App\Listeners;

use App\Events\TrackTaxGroupItemEvent;
use App\Models\History;
use App\Models\TaxGroupItem;

class TrackTaxGroupItemListener
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
     * @param TrackTaxGroupItemEvent $event
     * @return void
     */
    public function handle(TrackTaxGroupItemEvent $event): void
    {
        $data = $event->item;
        /** @var TaxGroupItem $taxGroupItem */
        $taxGroupItem = TaxGroupItem::withTrashed()->findOrFail($data['id']);

        History::query()->create([
            'reference_table' => 'App\Models\TaxGroupItem',
            'reference_id' => $taxGroupItem->getKey(),
            'actor_id' => auth()->id() ?? 5,
            'payload' => json_encode([
                'tax_group_id' => $taxGroupItem->tax_group_id,
                'name' => $data['name'],
                'is_percent' => $data['is_percent'],
                'end_subtraction' => $data['end_subtraction'],
                'value' => $data['value'],
                'order' => $data['order'],
            ])
        ]);
    }
}
