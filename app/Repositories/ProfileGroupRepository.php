<?php
declare(strict_types=1);

namespace App\Repositories;

use App\ProfileGroup as Model;
use App\Repositories\CoreRepository;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

final class ProfileGroupRepository extends CoreRepository
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
        return $this->model()->findOrFail($id);
    }

    /**
     * Получить активные группу ID => Название.
     *
     * @param bool|int $is_active
     * @return array
     */
    public function getGroupsIdNameWithPluck(bool $is_active = false): array
    {
        return $this->model()->where('active', $is_active)->get()->pluck('name','id')->toArray();
    }

    /**
     * Название и айди группы.
     *
     * @return object
     */
    public function getGroupsIdName(): object
    {
        return $this->model()->select('name', 'id')->get();
    }

    /**
     * Получить активные группы название и айди.
     * @return object
     */
    public function getActiveGroupsIdName(): object
    {
        return $this->model()->where('active',1)->select('name', 'id')->get();
    }

    /**
     * @return object
     */
    public function getActive(): object
    {
        return $this->model()->where('active', 1)->get();
    }

    /**
     * Проверка пользователя на доступ редактирование
     *
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

    /**
     * Удалить группу.
     *
     * @param int $id
     * @return bool
     */
    public function deactivateGroup(
        int $id
    ): bool
    {
        return $this->model()->findOrFail($id)->update([
            'active' => 0,
            'archived_date' => Carbon::now()->toDateTimeString()
        ]);
    }

    /**
     * Восстановить группу.
     *
     * @param int $id
     * @return bool
     */
    public function restoreOrIgnore(
        int $id
    ): bool
    {
        $group = $this->model()->findOrFail($id);

        if ($group->active == 0)
        {
            return $group->update([
                'active' => 1,
                'archived_date' => null
            ]);
        }

        return false;
    }

    /**
     * Все база знаний для группы.
     *
     * @param int $id
     * @return array
     */
    public function knowBasesBook(
        int $id
    ): array
    {
        return $this->model()->findOrFail($id)->knowBases()->where('access', 1)->get()->unique()->pluck('book_id')->toArray();
    }

    /**
     * Все бонусы для группы.
     *
     * @param int $id
     * @return mixed
     */
    public function bonuses(
        int $id
    )
    {
        return $this->getGroup($id)->bonuses;
    }

    /**
     * Все показатели для группы.
     *
     * @param int $id
     * @return mixed
     */
    public function activities(
        int $id
    )
    {
        return $this->getGroup($id)->activities;
    }

    /**
     * @param int $id
     * @return mixed'
     */
    public function paymentTerms(
        int $id
    )
    {
        return $this->getGroup($id)->payment_terms;
    }

    /**
     * @param int $userId
     * @param int $groupId
     * @return void
     */
    public function setHead(
        int $userId,
        int $groupId
    ): void
    {
        $group = $this->getGroup($groupId);
        $heads = json_decode($group->head_id);
        $heads[] = $userId;

        $group->update([
            'head_id' => json_encode($heads)
        ]);
    }
}