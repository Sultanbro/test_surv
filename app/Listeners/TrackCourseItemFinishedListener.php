<?php

namespace App\Listeners;

use App\Events\TrackCourseItemFinishedEvent;
use App\Models\Award;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
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
            $course = $event->course;
            $award  = Award::query()->where('course_id', $course->id)->first();
            $award->users()->attach($event->userId);

        } catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
