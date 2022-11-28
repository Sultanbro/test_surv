<?php

namespace App\Repositories;
use App\Models\Award\AwardType as Model;

class AwardTypeRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id): mixed
    {
        return $this->model()->find($id);
    }

    /**
     * Все типы награды
     */
    public function allTypes()
    {
        return $this->model()->get([
            'id',
            'name'
        ]);
    }
}