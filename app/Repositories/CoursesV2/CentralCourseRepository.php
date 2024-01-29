<?php

namespace App\Repositories\CoursesV2;

use App\DTO\CoursesV2\CoursePropsDto;
use App\Models\CentralCourse as Model;
use App\Repositories\CoreRepository;

/**
 * Класс для работы с Repository.
 */
class CentralCourseRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    public function filter()
    {
        return $this->model()->query()->paginate(10);
    }

    public function createCourse(CoursePropsDto $dto)
    {
        return $this->model()
            ->query()
            ->create([
                'tenant_id' => tenant('id'),
                'cat_id' => $dto->cat_id,
                'price' => $dto->price,
                'for_sale' => $dto->for_sale,
                'author' => $dto->author,
                'slides' => json_encode($dto->slides),
            ]);
    }

    public function updateCourse($centralCourseId, CoursePropsDto $dto)
    {
        return $this->model()
            ->query()
            ->where('id', $centralCourseId)
            ->update([
                'cat_id' => $dto->cat_id,
                'price' => $dto->price,
                'for_sale' => $dto->for_sale,
                'author' => $dto->author,
                'slides' => json_encode($dto->slides),
            ]);
    }

    public function delete($id)
    {
        $this->model()->query()->where('id', $id)->delete();
        return true;
    }
}
