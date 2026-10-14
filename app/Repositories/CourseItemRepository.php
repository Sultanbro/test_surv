<?php

namespace App\Repositories;

use App\Models\CourseItem as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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

    /**
     * Метод получает и массив id или один id и ищет по нему совпадение.
     *
     * @param array|int $ids
     * @return Builder
     */
    public function getItemByCourseIds(
        array|int $ids
    ): Builder
    {
        return $this->model()
            ->when(is_array($ids), fn($courseResults) => $courseResults->whereIn('course_id', $ids))
            ->when(is_int($ids), fn($courseResults) => $courseResults->where('course_id', $ids));
    }
}