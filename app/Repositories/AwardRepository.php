<?php

namespace App\Repositories;
use App\Models\Award\Award as Model;

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
     * @param string $operator
     * @return mixed
     */
    public function relationAwardUser($user,int  $type, string $operator = '='):array
    {
        $query = $this->model()
            ->join('award_user as au', 'au.award_id', '=', 'awards.id')
            ->join('award_types as at', 'at.id', '=', 'awards.award_type_id')
            ->join('users as u', 'u.id', '=', 'au.user_id')
            ->where('au.user_id', $operator, $user->id);

            if ($type){
                $query->where('at.id', $type);
            }
        return $query->get([
            'au.award_id',
            'awards.path',
            'awards.award_type_id',
            'awards.path',
            'awards.name',
            'awards.description',
            'awards.hide',
            'awards.styles',
            'au.user_id',
            'u.name',
            'u.last_name',
        ])->toArray();
    }

    /**
     * @param $user
     * @param $type
     * @return array
     */
    public function availableAwards($user, $type)
    {
        $query = $this->model()
            ->join('award_types as at', 'at.id', '=', 'awards.award_type_id');

            if ($type){
                $query->where('at.id', $type->id);
            }
            $query->whereDoesntHave('users',function ($q) use($user){
                $q->where('user_id', $user->id);
            });
            return $query->get([
                'awards.id',
                'awards.path',
                'awards.name',
                'awards.description',
                'awards.hide',
                'awards.styles',
                'award_type_id'
                ])->toArray();
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
    public function attachUser($award, $userId, $path = '')
    {

        return $award->users()->attach($userId, ['path' => $path]);
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