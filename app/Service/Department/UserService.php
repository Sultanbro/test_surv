<?php

namespace App\Service\Department;

use App\Events\TrackGroupChangingEvent;
use App\Models\GroupUser;
use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
        foreach ($groups as $group) {
            $groupUser = GroupUser::withTrashed()->select('user_id')->where('group_id', '=', $group->id)
                ->where(fn($query) => $query->whereYear('from', '<=', $this->getYear($date))->orWhereMonth('from', '<=', $this->getMonth($date)))
                ->where(fn($query) => $query->whereNull('to')->orWhere(
                    fn($query) => $query->whereYear('to', '<=', $this->getYear($date))->whereMonth('to', '>', $this->getMonth($date)))
                )->groupBy(['user_id'])
                ->havingRaw('count(user_id) >= ?', [1]);

            $data = $this->getGroupUsers($groupUser->get(), $date);
        }

        return $data;
    }

    /**
     * Все пользователи отдела который работает в настоящий время.
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getActualUsers(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);

        $data = [];
        foreach ($groups as $group) {
            $groupUser = GroupUser::withTrashed()
                ->select('user_id')
                ->where('group_id', '=', $group->id)
                ->where(fn($query) => $query->whereYear('from', '<=', $this->getYear($date))->orWhereMonth('from', '<=', $this->getMonth($date)))
                ->where(fn($query) => $query->whereNull('to')->orWhere(
                    fn($query) => $query->whereYear('to', '<=', $this->getYear($date))->whereMonth('to', '>', $this->getMonth($date)))
                )
                ->where("status", "active")
                ->groupBy(['user_id'])
                ->havingRaw('count(user_id) >= ?', [1]);

            $data = $this->getGroupUsers($groupUser->get(), $date);
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
        $data = collect();

        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $nextMonthFirstDay = Carbon::parse($date)->addMonth()->startOfMonth()->format('Y-m-d');

        foreach ($groups as $group) {
            $groupUser = GroupUser::withTrashed()
                ->where('group_id', '=', $group->id)
                ->whereDate('from', '<=', $last_date)
                ->where(fn($query) => $query->whereNull('to')->orWhere(
                    fn($query) => $query->whereDate('to', '>=', $nextMonthFirstDay))
                );

            $data = $data->merge($this->getGroupEmployees($groupUser->get(), $last_date));
        }

        return $data->unique(fn($u) => $u->id)->toArray();
    }

    /**
     * Все сотрудники из отдела.
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getEmployeesForSalaries(int $groupId, string $date): array
    {
        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $first_date = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $nextMonthFirstDay = Carbon::parse($date)->addMonth()->startOfMonth()->format('Y-m-d');

        $carbonDate = Carbon::parse($date);
        $currentMonth = Carbon::now()->month;
        $addCondition = false;
        if (Carbon::parse($date)->month < $currentMonth) {
            $addCondition = true;
            $nextMonth = $carbonDate->copy()->addMonth()->month;
        } else {
            $nextMonth = $carbonDate->month;
        }


        $data = User::withTrashed()->with('groups')->whereHas('group_users', function ($q) use ($nextMonth, $groupId, $first_date, $last_date, $nextMonthFirstDay, $addCondition) {
            $q->where('group_id', $groupId)
                ->where(function ($query) use ($nextMonth, $nextMonthFirstDay, $first_date, $last_date, $addCondition) {
                    $query->where(function ($subQuery) use ($nextMonth, $first_date, $last_date, $nextMonthFirstDay, $addCondition) {
                        // For active users in the selected month
                        $subQuery->where('status', GroupUser::STATUS_ACTIVE);
                        if ($addCondition) {
                            $subQuery->whereDate('from', '<', $nextMonthFirstDay);
                        }
                        $subQuery->where(function (Builder $query) use ($last_date, $nextMonthFirstDay) {
                            $query->whereBetween('to', [$last_date, $nextMonthFirstDay])
                                ->orWhereNull('to');
                        });
                    })->orWhere(function ($subQuery) use ($first_date, $last_date, $nextMonthFirstDay) {
                        // For users who were active and then dropped in the selected month
                        $subQuery->where('status', GroupUser::STATUS_DROP);
                        $subQuery->whereDate('from', '<=', $last_date);
                        $subQuery->whereDate('to', '>=', $last_date);
                    })->orWhere(function ($subQuery) use ($first_date, $last_date, $nextMonthFirstDay) {
                        // For users who were active and then dropped in the selected month
                        $subQuery->where('status', GroupUser::STATUS_FIRED);
                        $subQuery->whereDate('from', '<=', $last_date);
                        $subQuery->whereDate('to', '>', $last_date);
                    });
                });
        })
            ->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 0))
            ->where(function (Builder $query) use ($last_date) {
                $query->where('deleted_at', '>', $last_date)
                    ->orWhereNull('deleted_at');
            })
            ->get();

        return $data->unique(fn($u) => $u->id)->toArray();
    }

    /**
     * Получить id всех пользователей группы.
     *
     * @param $groupId
     * @param $date
     * @return array
     */
    public function getEmployeeIds($groupId, $date): array
    {
        return collect($this->getEmployees($groupId, $date))->pluck('id')->toArray();
    }

    /**
     * Получить id всех пользователей группы.
     *
     * @param $groupId
     * @param $date
     * @return array
     */
    public function getFiredEmployeeIds($groupId, $date): array
    {
        return collect($this->getFiredEmployees($groupId, $date))->pluck('id')->toArray();
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

        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $nextMonthFirstDay = Carbon::parse($date)->addMonth()->startOfMonth()->format('Y-m-d');

        foreach ($groups as $group) {
            $groupUser = GroupUser::withTrashed()
                ->where('group_id', '=', $group->id)
                ->whereDate('from', '<=', $last_date)
                ->where(fn($query) => $query
                    ->whereNull('to')
                    ->orWhere(
                        fn($query) => $query
                            ->whereDate('to', '>=', $nextMonthFirstDay))
                );

            $data = $this->getGroupsTrainees($groupUser->get());
        }

        return $data;
    }

    /**
     * Все стажеры из отдела.
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getTraineesForSalaries(int $groupId, string $date): array
    {
        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $nextMonthFirstDay = Carbon::parse($date)->addMonth()->startOfMonth()->format('Y-m-d');

        $data = User::with('groups')->whereHas('group_users', function ($q) use ($groupId, $last_date, $nextMonthFirstDay) {
            $q->where('status', GroupUser::STATUS_ACTIVE)
                ->where('group_id', $groupId)
                ->whereDate('from', '<=', $last_date)
                ->where(fn($query) => $query
                    ->whereNull('to')
                    ->orWhere(
                        fn($query) => $query
                            ->whereDate('to', '>=', $nextMonthFirstDay))
                );
        })
            ->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 1))
            ->get();

        return $data->unique(fn($u) => $u->id)->toArray();
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
        foreach ($groups as $group) {
            $groupUser = GroupUser::withTrashed()
                ->where('group_id', $group->id)
                ->where('status', GroupUser::STATUS_FIRED)
                ->whereYear('to', $this->getYear($date))->whereMonth('to', $this->getMonth($date));
            $firedUsers = $this->getGroupFiredUsers($groupUser->get(), $date);

            if (!empty($firedUsers)) {
                $data = $firedUsers;
            }
        }

        return $data;
    }

    /**
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getFiredEmployees(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);
        $data = [];
        foreach ($groups as $group) {
            $groupUser = GroupUser::withTrashed()->where('group_id', $group->id)
                ->whereIn('status', [GroupUser::STATUS_FIRED, GroupUser::STATUS_DROP])
                ->whereYear('to', $this->getYear($date))
                ->whereMonth('to', $this->getMonth($date));

            $data = $this->getGroupEmployees($groupUser->get());
        }

        return $data;
    }

    /**
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public function getFiredEmployeesForSalaries(int $groupId, string $date): array
    {
        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $nextMonthFirstDay = Carbon::parse($date)->addMonth()->startOfMonth()->format('Y-m-d');

        $data = User::withTrashed()
            ->with('groups')
            ->whereHas('group_users', function ($q) use ($groupId, $date, $last_date, $nextMonthFirstDay) {
                $q->whereIn('status', [GroupUser::STATUS_FIRED]);
                $q->where('group_id', $groupId);
                $q->whereYear('to', $this->getYear($date))->whereMonth('to', $this->getMonth($date));
            })->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 0))
            ->get();

        return $data->unique(fn($u) => $u->id)->toArray();
    }


    /**
     * @param int $groupId
     * @param string $date
     * @return array
     */
    public
    function getFiredTrainees(int $groupId, string $date): array
    {
        $groups = $this->getGroups($groupId);
        $data = [];
        foreach ($groups as $group) {
            $groupUser = GroupUser::withTrashed()->where('group_id', $group->id)
                ->whereYear('to', $this->getYear($date))->whereMonth('to', $this->getMonth($date));

            $firedUsers = $this->getGroupFiredTrainees($groupUser->get(), $date);

            if (!empty($firedUsers)) {
                $data = $firedUsers;
            }
        }

        return $data;
    }

    private
    function getGroupFiredTrainees($firedTrainees, $date): array
    {
        $firedTraineeData = [];
        foreach ($firedTrainees as $firedTrainee) {
            $user = User::withTrashed()->where('users.id', $firedTrainee->user_id)
                ->withWhereHas('description', fn($description) => $description->where('is_trainee', 1))->first();

            if (empty($user)) {
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
    private
    function getGroupFiredUsers($firedUsers, $date): array
    {
        $firedUserData = [];
        foreach ($firedUsers as $firedUser) {
            $user = User::onlyTrashed()->where('id', $firedUser->user_id)->first();

            if (empty($user)) {
                continue;
            }

            $firedUserData[] = $user;
        }

        return $firedUserData;
    }

    /**
     * @param $groupUsers
     * @return array
     */
    public
    function getGroupsTrainees($groupUsers): array
    {
        $traineesData = [];

        foreach ($groupUsers as $groupUser) {
            $user = User::query()->where('id', $groupUser->user_id)
                ->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 1))
                ->first();

            if ($user == null) {
                continue;
            }

            $traineesData[] = $user;
        }

        return $traineesData;
    }

    /**
     * @param $groupUsers
     * @param string|null $last_date - Если указана дата, то выводим список, отсекая уволенных после этой даты
     * @return array
     */
    private
    function getGroupEmployees($groupUsers, string $last_date = null): array
    {
        $userData = [];
        foreach ($groupUsers as $groupUser) {
            $user = User::withTrashed()
                ->with('groups')
                ->where('id', $groupUser->user_id)
                ->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 0));

            if ($last_date) {
                $user = $user->where(function (Builder $query) use ($last_date) {
                    $query->where('deleted_at', '>', $last_date)
                        ->orWhereNull('deleted_at');
                });
            }

            $user = $user->first();

            if ($user == null) {
                continue;
            }

            $userData[] = $user;
        }

        return $userData;
    }

    /**
     * @param $groups
     * @return array
     */
    private
    function getGroupUsers($groups): array
    {
        $userData = [];
        foreach ($groups as $group) {
            $user = User::withTrashed()->where('id', $group->user_id)->first();

            if ($user) {
                $userData[] = $user;
            }
        }

        return $userData;
    }

    /**
     * Получить с уволенными.
     * @return void
     */
    public
    function getTraineesWithTrashed(): void
    {
        //
    }

    /**
     * Получаем группы если она не передано, а если есть то берем по ID.
     * @param int $groupId
     * @return array|Builder[]|Collection|HigherOrderWhenProxy[]
     */
    private
    function getGroups(int $groupId): Collection|array
    {
        return ProfileGroup::query()->when($groupId != 0, function ($group) use ($groupId) {
            $group->where('id', $groupId);
        })->get();
    }

    /**
     * @param string $date
     * @return int
     */
    private
    function getYear(string $date): int
    {
        return $date == null ? Carbon::now()->year : Carbon::createFromFormat('Y-m-d', $date)->year;
    }

    /**
     * @param string $date
     * @return int
     */
    private
    function getMonth(string $date): int
    {
        return $date == null ? Carbon::now()->month : Carbon::createFromFormat('Y-m-d', $date)->month;
    }

    public function getEmployeesWithFired(int $groupId, Carbon|string $date): Collection
    {
        $date = is_string($date) ? Carbon::parse($date) : $date;

        return User::withTrashed()
            ->selectRaw('users.id as id, users.deleted_at as deleted_at')
            ->join('group_user as pivot', 'users.id', '=', 'pivot.user_id')
            ->join('profile_groups as g', 'g.id', '=', 'pivot.group_id')
            ->where('g.id', $groupId)
            ->where(function ($query) use ($date) {
                $query->whereNull('pivot.to');
                $query->orWhere(function (Builder $query) use ($date) {
                    $start = $date->startOfMonth()->format("Y-m-d");
                    $end = $date->endOfMonth()->format("Y-m-d");
                    $query->whereBetween('pivot.to', [$start, $end]);
                });
            })
            ->groupBy('users.id')
            ->get();
    }

    /**
     * @param $date
     * @return mixed|string
     */
    private
    function getFullDate($date)
    {
        return $date == null ? Carbon::now()->toDateString() : $date;
    }

    /**
     * Удалить из группы
     * @param int $userID
     * @param String|null $date
     * @return void
     */
    public
    function fireUser(int $userID, $date = null): void
    {
        if ($date == null) $date = date('Y-m-d');

        GroupUser::where('user_id', $userID)
            ->whereNull('to')
            ->update([
                'to' => $date,
                'status' => 'fired',
            ]);
    }

    /**
     * @param int $userId
     * @return void
     */
    public
    function restoredUser(int $userId): void
    {
        GroupUser::where('user_id', $userId)
            ->whereNotNull('to')
            ->where('status', 'fired')
            ->update([
                'to' => null,
                'status' => 'active',
            ]);
    }

    /**
     * Get all users in Department
     *
     * @param int $group_d
     * @param String $date
     * @return Collection
     */
    public
    function getUsersAll(int $group_id, string $date)
    {
        // working users - employees
        $users = $this->getEmployees(
            $group_id,
            Carbon::parse($date)->startOfMonth()->format('Y-m-d')
        );

        $users = collect($users);

        // trainees
        $trainees = $this->getTrainees(
            $group_id,
            Carbon::parse($date)->startOfMonth()->format('Y-m-d')
        );

        $users = $users->merge(collect($trainees));

        // fired users
        $fired = $this->getFiredEmployees(
            $group_id,
            Carbon::parse($date)->startOfMonth()->format('Y-m-d')
        );

        $users = $users->merge(collect($fired));

        return $users;
    }

    /**
     * @throws Exception
     */
    public
    function setGroup(
        int    $groupId,
        int    $userId,
        string $action
    ): void
    {
        $group = ProfileGroup::query()->find($groupId);
        $exist = $group->users()->where([
            ['user_id', $userId],
            ['status', 'active']
        ])->whereNull('to')->exists();

        try {
            if ($action == 'add' && !$exist) {
                $group->users()->attach($userId, [
                    'from' => Carbon::now()->toDateString()
                ]);
            }

            if ($action == 'delete') {
                event(new TrackGroupChangingEvent($userId, $groupId));
                $group->users()->where('user_id', $userId)->whereNull('to')->update([
                    'to' => Carbon::now()->toDateString(),
                    'status' => 'drop'
                ]);
            }
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }


    public function getEmployeesAll($group_id, string $date)
    {
        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $nextMonthFirstDay = Carbon::parse($date)->addMonth()->startOfMonth()->format('Y-m-d');

        $working = GroupUser::withTrashed()
            ->where('group_id', '=', $group_id)
            ->whereDate('from', '<=', $last_date)
            ->where(fn($query) => $query->whereNull('to')->orWhere(
                fn($query) => $query->whereDate('to', '>=', $nextMonthFirstDay))
            );
        return $this->getGroupEmployeesAll($working->get(), $last_date);
    }

    public function getFiredEmployeesAll($group_id, string $date)
    {
        $fired = GroupUser::withTrashed()
            ->where('group_id', $group_id)
            ->where('status', GroupUser::STATUS_FIRED)
            ->whereYear('to', $this->getYear($date))->whereMonth('to', $this->getMonth($date));

        return $this->getGroupEmployeesAll($fired->get());
    }

    public function getGroupEmployeesAll($group_users, $last_date = null, $withGroups = false)
    {
        $user_ids = [];
        foreach ($group_users as $key => $groupUser) {
            $user_ids[] = $groupUser->user_id;
        }

        $usersQuery = User::withTrashed()
            ->whereIn('id', $user_ids)
            ->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 0));

        if ($withGroups) {
            $usersQuery = $usersQuery->with('groups');
        }

        if ($last_date) {
            $usersQuery = $usersQuery->where(function (Builder $query) use ($last_date) {
                $query->where('deleted_at', '>', $last_date)->orWhereNull('deleted_at');
            });
        }

        return $usersQuery->get();
    }
}
