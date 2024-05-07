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

    /**
     * @param array $data
     * @return void
     */
    public function createOrUpdateMultipleCard(array $data): void
    {
        if($data['user_id'] && $data['number'] && $data['cardholder']){
            $this->model()->updateOrCreate([
                'user_id' => $data['user_id'],
                'number' => $data['number'],
                'cardholder' => $data['cardholder'],
            ],[
                'bank' => $data['bank'],
                'phone' => $data['phone'],
                'country' => $data['country'],
                'iban' => $data['iban'] ?? 0
            ]);
        }
    }
}
