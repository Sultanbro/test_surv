<?php
declare(strict_types=1);

namespace App\Repositories;

use App\ProfileGroup as Model;
use App\Repositories\CoreRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            $editors_id = json_decode($group->editors_id);
            if($editors_id == null) return $group;
            if(!in_array($userId, json_decode($editors_id)))
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

    /**
     * @param int $groupId
     * @param array $relations
     * @return object
     */
    public function profileGroupWithRelation(
        int $groupId,
        array $relations = []
    ): object
    {
        return $this->model()->with($relations)->find($groupId);
    }

    /**
     * @param Model $group
     * @param $users
     * @return void
     */
    public function storeMultipleUsers(
        Model $group,
        $users
    ): void
    {
        $data = [];

        foreach ($users as $userId)
        {
            $exist = $group->users()
                ->where('user_id', $userId)
                ->exists();

            if (!$exist)
            {
                $data[] = [
                    'user_id'    => $userId,
                    'group_id'   => $group->id,
                    'from'       => Carbon::now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        DB::table('group_user')->insert($data);
    }

    /**
     * @param Model $group
     * @param array $data
     * @return void
     */
    public function updateGroupData(
        Model $group,
        array $data
    ): void
    {
        $group->update($data);
    }

    /**
     * @param int $groupId
     * @param int|null $dialerId
     * @param int|null $scriptId
     * @param int|null $talkHours
     * @param int|null $talkMinutes
     * @return void
     */
    public function updateOrCreateDialer(
        int $groupId,
        ?int $dialerId,
        ?int $scriptId,
        ?int $talkHours,
        ?int $talkMinutes
    ): void
    {
        $this->getGroup($groupId)->dialer()->updateOrCreate(
            [
                'dialer_id' => $dialerId
            ],
            [
                'script_id'     => $scriptId,
                'talk_hours'    => $talkHours,
                'talk_minutes'  => $talkMinutes
            ]
        );

    }
}