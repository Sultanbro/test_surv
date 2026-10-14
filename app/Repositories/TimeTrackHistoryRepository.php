<?php

namespace App\Repositories;

use App\TimetrackingHistory as Model;
use App\User;

/**
* Класс для работы с Repository.
*/
class TimeTrackHistoryRepository extends CoreRepository
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
     * @param string $description
     * @param string $date
     * @return mixed
     */
    public function createHistory(
        int $userId,
        string $description,
        string $date
    )
    {
        return $this->model()->create([
            'author_id' => auth()->id() ?? 5,
            'author'    => auth()->user()->full_name ?? User::find(5)->full_name,
            'user_id'   => $userId,
            'description' => $description,
            'date'      => $date
        ]);
    }
}