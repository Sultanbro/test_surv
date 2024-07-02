<?php

namespace App\Events;

use App\Models\Course;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackCourseItemFinishedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Course
     */
    public Course $course;

    /**
     * @var int
     */
    public int $userId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Course $course, int $userId)
    {
        $this->course = $course;
        $this->userId = $userId;
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
