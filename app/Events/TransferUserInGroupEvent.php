<?php

namespace App\Events;

use App\ProfileGroup;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class TransferUserInGroupEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $newGroup;

    public ProfileGroup $oldGroup;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($oldGroup, $newGroup)
    {
        $this->oldGroup = $oldGroup;
        $this->newGroup = $newGroup;
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
