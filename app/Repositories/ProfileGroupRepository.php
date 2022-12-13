<?php

namespace App\Repositories;

use App\ProfileGroup as Model;
use App\Repositories\CoreRepository;

class ProfileGroupRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getGroup($id)
    {
        return $this->model()->find($id);
    }
}