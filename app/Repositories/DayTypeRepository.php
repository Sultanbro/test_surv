<?php

namespace App\Repositories;

use App\DayType as Model;
use Illuminate\Support\Facades\Auth;

/**
* Класс для работы с Repository.
*/
class DayTypeRepository extends CoreRepository
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
     * @param int $userId
     * @return mixed
     */
    public function createNew(
        int $userId
    )
    {
        return $this->model()->create([
            'user_id' => $userId,
            'type' => Model::DAY_TYPES['TRAINEE'], // Стажировка
            'email' => 'x',
            'date' => date('Y-m-d'),
            'admin_id' => Auth::user()->id ?? 5,
        ]);
    }
}