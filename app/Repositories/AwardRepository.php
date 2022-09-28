<?php

namespace App\Repositories;
use App\Models\Award as Model;

class AwardRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id): mixed
    {
        return $this->model()->find($id);
    }

    /**
     * Связь между award и user.
     * Pivot таблица: award_user
     * @param $user
     * @param $operator
     * @return mixed
     */
    public function relationAwardUser($user, $operator = '=')
    {
        return $this->model()->join('award_user as au', 'au.award_id', '=', 'awards.id')
            ->where('au.user_id', $operator, $user->id)->get()->toArray();
    }

    /**
     * Получаем все награды с типом Номинаций.
     * @return mixed
     */
    public function getNomination()
    {
        return $this->model()->join('award_types as at', 'at.id', '=', 'awards.award_type_id')
                ->where('at.name', 'like', '%Номинаций%')->get()->toArray();
    }

    /**
     * Вознаграждаем сотрудника в профиле.
     * @param $id
     * @param $userId
     * @return mixed
     */
    public function attachUser($id, $userId)
    {
        return $this->getById($id)->users()->attach($userId);
    }

    /**
     * Удаляем вознаграждение для сотрудника.
     * @param $id
     * @param $userId
     * @return mixed
     */
    public function detachUser($id, $userId)
    {
        return $this->getById($id)->users()->detach($userId);
    }
}