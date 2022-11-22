<?php

namespace App\Repositories;

use App\Models\CourseItem as Model;

class CourseItemRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param int $courseItemId
     * @return mixed
     */
    public function getCourse(int $courseItemId)
    {
        return $this->model()->findOrFail($courseItemId)->course()->first();
    }
}