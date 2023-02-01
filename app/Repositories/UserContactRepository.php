<?php

namespace App\Repositories;

use App\UserContact as Model;

/**
* Класс для работы с Repository.
*/
class UserContactRepository extends CoreRepository
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
     * Сохранение доп телефонов для пользователя.
     *
     * @param array $data
     * @return void
     */
    public function createMultipleContact(
        array $data
    ): void
    {
        $this->model()->insert($data);
    }

    public function getContracts(
        string $value,
        string $name,
        int $userId,
        string $operator = '='
    )
    {
        return $this->model()->where([
            ['type', $operator, 'phone'],
            ['value', $operator, $value],
            ['name', $operator, $name]
        ]);
    }
}