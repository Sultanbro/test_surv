<?php

namespace App\Repositories;

use App\ProfileGroup as Model;
use App\Repositories\CoreRepository;

class ProfileGroupRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getGroup($id)
    {
        return $this->model()->find($id);
    }

    /**
     * Получить активные группу ID => Название.
     *
     * @param bool|int $is_active
     * @return array
     */
    public function getGroupsIdNameWithPluck(bool $is_active = false): array
    {
        return $this->model()->where('active', $is_active)->get(['id', 'name'])->pluck('name','id')->toArray();
    }

    /**
     * @return object
     */
    public function getGroupsIdName(): object
    {
        return $this->model()->select('name', 'id')->get();
    }

    /**
     * @return object
     */
    public function getActive(): object
    {
        return $this->model()->where('active', 1)->get();
    }

    /**
     * @param $userId
     * @return object
     */
    public function checkEditor($userId): object
    {
        return $this->getActive()->reject(function ($group) use ($userId) {
             if(!in_array($userId, json_decode($group->editors_id)))
             {
                 return $group;
             };
        });
    }
}