<?php

namespace App\Repositories;
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
            'is_trainee' => 0,
            'applied' => now()
        ]);
    }
}