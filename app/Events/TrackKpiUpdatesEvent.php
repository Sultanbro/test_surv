<?php

namespace App\Events;

use App\Models\Kpi\Kpi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackKpiUpdatesEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $kpiId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $kpiId)
    {
        $this->kpiId = $kpiId;
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
