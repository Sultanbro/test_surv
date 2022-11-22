<?php

namespace App\Repositories;

use App\Models\CourseResult as Model;

class CourseResultRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param int $courseId
     * @param int $userId
     * @return mixed
     */
    public function resetResult(
        int $courseId,
        int $userId
    )
    {
        return $this->model()->where([
            ['course_id', $courseId],
            ['user_id', $userId]

        ])->update([
            'status'    => Model::INITIAL,
            'progress'  => 0,
            'points'    => 0,
            'weekly_progress' => null,
            'ended_at'  => null
        ]);
    }

    public function removeItemPoints(
        int $itemId
    )
    {
        //
    }
}