<?php

namespace App\Listeners;

use App\Events\TrackCourseItemFinishedEvent;
use App\Models\Award\Award;
use Exception;
use Throwable;

class TrackCourseItemFinishedListener
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
     * @param TrackCourseItemFinishedEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(TrackCourseItemFinishedEvent $event): void
    {
        try {
            
            $award  = Award::where('course_id', $event->course->id)->first();

            if($award) {
                $award->users()->attach($event->userId);
            }

        } catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
