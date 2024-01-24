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
}
