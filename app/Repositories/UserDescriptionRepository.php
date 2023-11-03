<?php

namespace App\Repositories;

use App\UserDescription;
use App\UserDescription as Model;

/**
 * Шаблон Repository для налогов.
 */
class UserDescriptionRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * Меняем с стажера на сотрудника.
     *
     * @param $userId
     * @return void
     */
    public function setEmployee($userId): void
    {
        $this->model()->where('user_id', $userId)->update([
            'is_trainee' => 1
        ]);
    }

    /**
     * @param int $userId
     * @return void
     */
    public function setTrainee(
        int $userId
    ): void
    {
        $this->model()->create([
            'user_id' => $userId,
            'is_trainee' => true,
        ]);
    }

    public function touchDescription(
        int  $userId,
        bool $isTrainee
    ): void
    {
        UserDescription::query()->updateOrCreate(
            [
                'user_id' => $userId,
            ],
            [
                'is_trainee' => $isTrainee,
                'applied' => $isTrainee ? now()->format("Y-m-d") : null
            ]
        );
    }
}
