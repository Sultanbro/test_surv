<?php

namespace App\DTO;

use App\Models\Course;
use App\User;

class CourseProgressDTO
{
    public function __construct(
        public User $user,
        public Course $course,
    )
    {
    }

    public static function toArray(
        int $userId,
        int $courseId
    )
    {
        $user   = User::query()->findOrFail($userId);
        $course = Course::query()->findOrFail($courseId);

        return new self(
            $user,
            $course,
        );
    }
}