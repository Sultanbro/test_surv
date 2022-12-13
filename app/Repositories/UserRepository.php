<?php

namespace App\Repositories;

use App\User as Model;
use Illuminate\Support\Facades\DB;

/**
* Класс для работы с Repository.
*/
class UserRepository extends CoreRepository
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
     */
    public function getIdFullName(): array
    {
        return $this->model()->withTrashed()->select(DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"), 'ID as id')->get()->toArray();
    }
}