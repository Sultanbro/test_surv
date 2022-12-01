<?php

namespace App\DTO;

use App\Models\Course;
use App\Models\CourseItem;
use App\Models\CourseItemModel;
use App\Models\TestBonus;
use App\User;
use Illuminate\Database\Eloquent\Collection;

class CourseProgressDTO
{
    public function __construct(
        public User $user,
        public Course $course,
        public Collection $courseItems,
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

        $courseItems = CourseItem::with('element')
            ->where('course_id', $courseId)
            ->get()
            ->map(function($item) use ($userId) {
                $item->element_id = $item->element ? $item->element->id : 0;
        
                $item->stages = $item->element ? count($item->element->getOrder()) : 0;
                $item->passed_stages = $item->element
                    ? (new CourseItemModel)->progress($userId, $item->id, $item->element)
                    : [];

                $item->bonuses = TestBonus::query()
                                    ->where('course_item_id', $item->id)
                                    ->where('user_id', $userId)
                                    ->get();

                unset($item->element);
                return $item;
            });

        return new self(
            $user,
            $course,
            $courseItems
        );
    }
}