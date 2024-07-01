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

    /**
     * @param string $value
     * @param string $name
     * @param int $userId
     * @param string $operator
     * @return object|null
     */
    public function getContracts(
        string $value,
        string $name,
        int $userId,
        string $operator = '='
    ): ?object
    {
        return $this->model()->where([
            ['type', $operator, 'phone'],
            ['value', $operator, $value],
            ['name', $operator, $name]
        ]);
    }

    /**
     * @param int $userId
     * @return object|null
     */
    public function getAllUserContacts(
        int $userId
    ): ?object
    {
        return $this->model()->where('user_id', $userId);
    }
}