<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackGroupChangingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    public int $groupId;

    /**
     * ID сотрудника.
     * @var int
     */
    public int $userId;

    /**
     * Действие:
     * Добавить или удалить
     *
     * @var string
     */
    public string $action;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $action, $groupId)
    {
        $this->userId  = $userId;
        $this->action  = $action;
        $this->groupId = $groupId;
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
