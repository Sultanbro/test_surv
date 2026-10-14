<?php

namespace App\Repositories;

use App\Models\Course as Model;

class CourseRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param int $courseId
     * @return mixed
     */
    public function getCourseItems(int $courseId)
    {
        return $this->model()->findOrFail($courseId)->items()->get();
    }
}