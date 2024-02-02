<?php

namespace App\Repositories\CoursesV2;

use App\DTO\CoursesV2\CourseGradePropsDto;
use App\Models\CourseGradeV2 as Model;
use App\Repositories\CoreRepository;
use App\Models\CourseV2;
use App\ProfileGroup;
use App\Position;
use App\User;

/**
 * Класс для работы с Repository.
 */
class MyCourseV2Repository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function getCourses()
    {
        /** @var User $user */
        $user = auth()->user();

        $user_id = $user->id;
        $position_id = $user->position_id;
        $groups = $user->inGroups()->pluck('id')->toArray();

        return CourseV2::query()
            ->where(function ($q) use ($position_id, $groups, $user_id) {
                $q->whereHas('users', function ($q) use ($user_id) {
                    $q->where(function ($q) use ($user_id) {
                        $q->where('course_targets_v2.target_id', $user_id)
                            ->where('course_targets_v2.target_type', User::class);
                    });
                })
                    ->orWhereHas('groups', function ($q) use ($groups) {
                        $q->where(function ($q) use ($groups) {
                            $q->whereIn('course_targets_v2.target_id', $groups)
                                ->where('course_targets_v2.target_type', ProfileGroup::class);
                        });
                    })
                    ->orWhereHas('positions', function ($q) use ($position_id) {
                        $q->where(function ($q) use ($position_id) {
                            $q->where('course_targets_v2.target_id', $position_id)
                                ->where('course_targets_v2.target_type', Position::class);
                        });
                    });
            })
            ->with('userCourseProgress', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->orderBy('order')
            ->get();
    }

    public function createCourseGrade(CourseGradePropsDto $dto, $user_id)
    {
        return $this->model()->query()->create([
            'user_id' => $user_id,
            'course_id' => $dto->course_id,
            'course_grade' => $dto->course_grade,
            'curator_grade' => $dto->curator_grade,
            'course_comment' => $dto->course_comment,
            'curator_comment' => $dto->curator_comment,
            'curator_id' => $dto->curator_id,
        ]);
    }

    public function delete(Model $grade)
    {
        $grade->delete();
        return true;
    }
}
