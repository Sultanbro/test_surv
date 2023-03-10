<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KpiChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $year;
    public int $month;

    /**
     * Create a new event instance.
     *
     * @param Carbon $date
     * @return void
     */
    public function __construct(Carbon $date)
    {
        $this->year = $date->year;
        $this->month = $date->month;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
