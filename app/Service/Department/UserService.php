<?php

namespace App\Service\Department;

use App\Models\GroupUser;
use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HigherOrderWhenProxy;

class UserService
{
    /**
     * @param int $user
     * @return array
     */
    public function userIn(int $user): array
    {
        return User::with('groups')->findOrFail($user)->toArray();
    }

    /**
     * Все пользователи из отдела.
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getUsers(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);

        $data = [];
        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()->where([
                ['group_id', $group->id],
            ])
                ->where('created_at', '<', $this->getFullDate($date))
                ->where(fn ($query) =>  $query
                    ->whereNull('deleted_at')
                    ->orWhere('deleted_at', '>', $this->getFullDate($date))
            );

            $data[] = $this->getGroupUsersFromHistories($groupUser->get(), $date);
        }

        return $data;
    }

    /**
     * Все сотрудники из отдела.
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getEmployees(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);
        $data = [];
        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()->where([
                ['group_id', $group->id],
            ])
                ->where('created_at', '<', $this->getFullDate($date))
                ->where(fn ($query) =>  $query
                    ->whereNull('deleted_at')
                    ->orWhere('deleted_at', '>', $this->getFullDate($date))
                );

            $data[] = $this->getGroupEmployees($groupUser->get(), $date);
        }

        return $data;
    }

    /**
     * Все стажеры из отдела.
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getTrainees(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);
        $data = [];
        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()->where([
                ['group_id', $group->id],
            ])->where('created_at', '<', $this->getFullDate($date))
                ->where(fn ($query) =>  $query
                    ->whereNull('deleted_at')
                    ->orWhere('deleted_at', '>', $this->getFullDate($date))
                );

            $data[] = $this->getGroupsTrainees($groupUser->get(), $date);
        }

        return $data;
    }

    /**
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getFiredUsers(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);
        $data = [];
        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()->where('group_id', $group->id)
                ->where(fn ($query) => $query->whereYear('deleted_at', $this->getYear($date))
                        ->whereMonth('deleted_at', $this->getMonth($date))
                        ->orWhereNull('deleted_at')
                );

            $firedUsers = $this->getGroupFiredUsers($groupUser->get(), $date);

            if (!empty($firedUsers)) {
                $data[] = $firedUsers;
            }
        }

        return $data;
    }

    /**
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getFiredTrainees(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);
        $data = [];
        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()->where('group_id', $group->id)
                ->where(function ($query) use ($date) {
                    $query
                        ->whereYear('deleted_at', $this->getYear($date))
                        ->whereMonth('deleted_at', $this->getMonth($date))
                        ->orWhereNull('deleted_at');
                });

            $firedUsers = $this->getGroupFiredTrainees($groupUser->get(), $date);

            if (!empty($firedUsers)) {
                $data[] = $firedUsers;
            }
        }

        return $data;
    }

    private function getGroupFiredTrainees($firedTrainees, $date)
    {
        $firedTraineeData = [];
        foreach ($firedTrainees as $firedTrainee)
        {
            $user = User::withTrashed()->where('users.id', $firedTrainee->user_id)
                ->withWhereHas('userDescription', fn($description) => $description->where('is_trainee', 1))
                ->when($firedTrainee->deleted_at == null, fn ($user) => $user->withWhereHas(
                        'histories', fn($history) => $history
                        ->whereYear('payload->action_date', $this->getYear($date))
                        ->whereMonth('payload->action_date', $this->getMonth($date))
                    ))->first();

            if (empty($user)){
                continue;
            }

            $firedTraineeData[] = $user;
        }

        return $firedTraineeData;
    }

    /**
     * @param $firedUsers
     * @param $date
     * @return array
     */
    private function getGroupFiredUsers($firedUsers, $date): array
    {
        $firedUserData = [];
        foreach ($firedUsers as $firedUser)
        {
            $user = User::withTrashed()->where('users.id', $firedUser->user_id)
                ->when($firedUser->deleted_at == null, fn ($user) =>
                    $user->withWhereHas(
                    'histories', fn($history) => $history
                        ->whereYear('payload->action_date', $this->getYear($date))
                        ->whereMonth('payload->action_date', $this->getMonth($date))
                ))->first();

            if (empty($user)){
                continue;
            }

            $firedUserData[] = $user;
        }

        return $firedUserData;
    }

    /**
     * @param $groupUsers
     * @param $date
     * @return array
     */
    private function getGroupsTrainees($groupUsers, $date): array
    {
        $traineesData = [];
        foreach ($groupUsers as $groupUser)
        {
            $user = User::withTrashed()->where('users.id', $groupUser->user_id)
                ->with(
                    [
                        'histories' => fn($history) => $history->where('payload->action_date', '<', $this->getFullDate($date))
                            ->where('payload->action', 'delete')
                            ->where('payload->group_id', $groupUser->group_id),
                        'userDescription' => fn($description) => $description->where('is_trainee', 1)
                    ]
                )->whereHas('userDescription', fn($description) => $description->where('is_trainee', 1))
                ->first();

            if ($user == null){
                continue;
            }

            $traineesData[] = $user;
        }

        return $traineesData;
    }

    /**
     * @param $groupUsers
     * @param $date
     * @return array
     */
    private function getGroupEmployees($groupUsers, $date): array
    {
        $userData = [];
        foreach ($groupUsers as $groupUser)
        {
            $user = User::withTrashed()->where('users.id', $groupUser->user_id)
                ->with(
                    [
                        'histories' => fn($history) => $history->where('payload->action_date', '<', $this->getFullDate($date))
                            ->where('payload->action', 'delete')
                            ->where('payload->group_id', $groupUser->group_id),
                        'userDescription' => fn($description) => $description->where('is_trainee', 0)
                    ]
                )->whereHas('userDescription', fn($description) => $description->where('is_trainee', 0))
                ->first();

            if ($user == null) {
                continue;
            }

            $userData[] = $user;
        }

        return $userData;
    }

    /**
     * @param $groups
     * @param $date
     * @return array
     */
    private function getGroupUsersFromHistories($groups, $date): array
    {
        $userData = [];
        foreach ($groups as $group)
        {
            $user = User::withTrashed()->where('users.id', $group->user_id)
                ->when($group->deleted_at == null, fn ($user) =>
                    $user->with([
                        'histories' => fn($history) => $history->where('payload->action_date', '<', $this->getFullDate($date))
                            ->where('payload->action', 'delete')
                            ->where('payload->group_id', $group->group_id)
                    ]))->first();

            if (empty($user)){
                continue;
            }

            $userData[] = $user;
        }

        return $userData;
    }

    /**
     * Получаем группы если она не передано, а если есть то берем по ID.
     * @param int $groupId
     * @return array|Builder[]|Collection|HigherOrderWhenProxy[]
     */
    private function getGroups(int $groupId): Collection|array
    {
        return ProfileGroup::query()->when($groupId != 0, function ($group) use ($groupId){
            $group->where('id', $groupId);
        })->get();
    }

    /**
     * @param string $date
     * @return int
     */
    private function getYear(string $date): int
    {
        return $date == null ? Carbon::now()->year : Carbon::createFromFormat('Y-m-d', $date)->year;
    }

    /**
     * @param string $date
     * @return int
     */
    private function getMonth(string $date): int
    {
        return $date == null ? Carbon::now()->month : Carbon::createFromFormat('Y-m-d', $date)->month;
    }

    /**
     * @param $date
     * @return mixed|string
     */
    private function getFullDate($date)
    {
        return $date == null ? Carbon::now()->toDateString() :  $date;
    }
}