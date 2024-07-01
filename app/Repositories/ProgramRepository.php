<?php

namespace App\Repositories;

use App\Program as Model;

/**
* Класс для работы с Repository.
*/
class ProgramRepository extends CoreRepository
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
     * @return object
     */
    public function getProgramByDesc(): object
    {
        return $this->model()->orderBy('id', 'desc')->get();
    }
}