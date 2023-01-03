<?php

namespace App\Repositories;

use App\Models\User\Card as Model;

/**
* Класс для работы с Repository.
*/
class CardRepository extends CoreRepository
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
     * @param array $data
     * @return void
     */
    public function createMultipleCard(
        array $data
    ): void
    {
        $this->model()->insert($data);
    }
}