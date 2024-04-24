<?php

namespace App\Repositories\CoursesV2;

use App\Models\CentralCourseCat as Model;
use App\Repositories\CoreRepository;

/**
 * Класс для работы с Repository.
 */
class CentralCourseCatRepository extends CoreRepository
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

    public function createCat(array $data)
    {
        return $this->model()
            ->query()
            ->create([
                'name' => $data['name'],
                'order' => $data['order']
            ]);
    }

    public function updateCat(Model $category, $data)
    {
        return $category->update([
            'name' => $data['name'],
            'order' => $data['order'],
        ]);
    }

    public function delete(Model $category)
    {
        $category->delete();
        return true;
    }
}
