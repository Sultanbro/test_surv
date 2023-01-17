<?php
namespace App\Repositories\Permissions;

use App\Repositories\CoreRepository;
use App\Models\Permission as Model;

/**
* Класс для работы с Repository.
*/
class PermissionRepository extends CoreRepository
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

    /**
     * @param array $names
     * @param array $ids
     * @return mixed
     */
    public function multiFindByNameOrId(
        array $names = [],
        array $ids = []
    )
    {
        return $this->model()->whereIn('id', $ids)->orWhereIn('name', $names);
    }
}