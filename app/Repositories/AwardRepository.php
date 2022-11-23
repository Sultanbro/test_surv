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
    public function relationAwardUser($user, $type, $operator = '=')
    {
        $query = $this->model()
            ->join('award_user as au', 'au.award_id', '=', 'awards.id')
            ->join('award_types as at', 'at.id', '=', 'awards.award_type_id')
            ->where('au.user_id', $operator, $user->id);

            if ($type){
                $query->where('at.id', $type->id);
            }
           return  $query->get()->toArray();
    }

    /**
     * Получаем все награды с типом Номинаций.
     * @return mixed
     */
    public function getNomination($user): array
    {
        return $this->model()
            ->join('award_types as at', 'at.id', '=', 'awards.award_type_id')
            ->join('award_user as au', 'au.award_id', '=', 'awards.id')
            ->where('at.name', 'like', '%Номинаций%')
            ->where('au.user_id', '!=', $user->id)
            ->get()->toArray();
    }

    /**
     * Вознаграждаем сотрудника в профиле.
     * @param $id
     * @param $userId
     * @return mixed
     */
    public function attachUser($id, $userId, $path = '')
    {
        return $this->getById($id)->users()->attach($userId, ['path' => $path]);
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