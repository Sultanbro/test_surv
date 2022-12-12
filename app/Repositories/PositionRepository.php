<?php

namespace App\Repositories;

use App\Position as Model;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
* Класс для работы с Repository.
*/
class PositionRepository extends CoreRepository
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
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPositionIdNameWithPluck(): array
    {
        return $this->model()->get()->pluck('position','id')->toArray();
    }

    /**
     * @return array
     */
    public function getPositionIdName(): array
    {
        return $this->model()->select('position as name', 'id')->get()->toArray();
    }
}