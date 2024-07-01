<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Admin\EditedBonus as Model;

/**
* Класс для работы с Repository.
*/
class EditedBonusRepository extends CoreRepository
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
     * @param string $date
     * @return object|null
     */
    public function getUserEditedBonusPerDate(
        int $userId,
        string $date
    ): object|null
    {
        return $this->model()->where('user_id', $userId)
            ->whereYear('date', Carbon::parse($date)->year)
            ->whereMonth('date', Carbon::parse($date)->month)
            ->first();
    }

    /**
     * @param int $userId
     * @param string $amount
     * @param string $comment
     * @param string $date
     * @return void
     */
    public function createEditedBonus(
        int $userId,
        string $amount,
        string $comment,
        string $date
    ): void
    {
        $this->model()->create([
            'user_id' => $userId,
            'author_id' => auth()->id() ?? 5,
            'amount'    => $amount,
            'comment'   => $comment,
            'date'      => $date
        ]);
    }

    /**
     * @param int $userId
     * @param string $amount
     * @param string $comment
     * @param string $date
     * @return void
     */
    public function updateOrCreate(
        int $userId,
        string $amount,
        string $comment,
        string $date
    ): void
    {
        $this->model()->updateOrCreate(
            [
                'user_id' => $userId,
                'date'  => $date
            ],
            [
                'author_id' => auth()->id() ?? 5,
                'amount'    => $amount,
                'comment'   => $comment,
                'date'      => $date
            ]
        );
    }
}