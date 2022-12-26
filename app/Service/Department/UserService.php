<?php

namespace App\Service\Department;

use App\Models\GroupUser;
use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
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
        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()->select('user_id')->where('group_id','=',$group->id)
                ->where(fn ($query) => $query->whereYear('from','<=', $this->getYear($date))->orWhereMonth('from','<=',$this->getMonth($date)))
                ->where(fn ($query) => $query->whereNull('to')->orWhere(
                    fn ($query) => $query->whereYear('to','<=',$this->getYear($date))->whereMonth('to','>',$this->getMonth($date)))
                )->groupBy(['user_id'])
                ->havingRaw('count(user_id) >= ?',[1]);

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
        $data = [];

        $last_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $nextMonthFirstDay = Carbon::parse($date)->addMonth()->startOfMonth()->format('Y-m-d');

        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()
                ->where('group_id','=',$group->id)
                ->whereDate('from','<=', $last_date)
                ->where(fn ($query) => $query->whereNull('to')->orWhere(
                    fn ($query) => $query->whereDate('to', '>=', $nextMonthFirstDay))
                );

            $data = $this->getGroupEmployees($groupUser->get());
        }

        return $data;
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

        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()
                ->where('group_id','=',$group->id)
                ->whereDate('from','<=', $last_date)
                ->where(fn ($query) => $query->whereNull('to')->orWhere(
                    fn ($query) => $query->whereDate('to', '>=', $nextMonthFirstDay))
                );

            $data = $this->getGroupsTrainees($groupUser->get());
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
        $data   = [];
        foreach ($groups as $group)
        {
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
        $data   = [];
        foreach ($groups as $group)
        {
            $groupUser = GroupUser::withTrashed()->where('group_id', $group->id)
                ->where('status', GroupUser::STATUS_FIRED)
                ->whereYear('to', $this->getYear($date))->whereMonth('to', $this->getMonth($date));
            
            $data = $this->getGroupEmployees($groupUser->get());
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
                ->whereYear('to', $this->getYear($date))->whereMonth('to', $this->getMonth($date));

            $firedUsers = $this->getGroupFiredTrainees($groupUser->get(), $date);

            if (!empty($firedUsers)) {
                $data = $firedUsers;
            }
        }

        return $data;
    }

    private function getGroupFiredTrainees($firedTrainees, $date): array
    {
        $firedTraineeData = [];
        foreach ($firedTrainees as $firedTrainee)
        {
            $user = User::withTrashed()->where('users.id', $firedTrainee->user_id)
                ->withWhereHas('description', fn($description) => $description->where('is_trainee', 1))->first();

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
            $user = User::onlyTrashed()->where('id', $firedUser->user_id)->first();

            if (empty($user)){
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
    private function getGroupsTrainees($groupUsers): array
    {
        $traineesData = [];

        foreach ($groupUsers as $groupUser)
        {
            $user = User::query()->where('id', $groupUser->user_id)
                ->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 1))
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
     * @return array
     */
    private function getGroupEmployees($groupUsers): array
    {
        $userData = [];
        foreach ($groupUsers as $groupUser)
        {
            $user = User::withTrashed()->with('groups')->where('id', $groupUser->user_id)
                ->withWhereHas('user_description', fn($description) => $description->where('is_trainee', 0))
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
     * @return array
     */
    private function getGroupUsers($groups): array
    {
        $userData = [];
        foreach ($groups as $group)
        {
            $user = User::withTrashed()->where('id', $group->user_id)->first();

            if ($user){
                $userData[] = $user;
            }
        }

        return $userData;
    }

    /**
     * Получить с уволенными.
     * @return void
     */
    public function getTraineesWithTrashed(): void
    {
        //
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

    /**
     * Удалить из группы 
     * @param int $userID
     * @param String|null $date
     * @return void
     */
    public function fireUser(int $userID, $date = null) : void
    {
        if($date == null) $date = date('Y-m-d');

        GroupUser::where('user_id', $userID)
            ->whereNull('to')
            ->update([
                'to'     => $date,
                'status' => 'fired',
            ]);
    }

    /**
     * Get all users in Department
     * 
     * @param int $group_d
     * @param String $date
     * @return Collection
     */
    public function getUsersAll(int $group_id, String $date) 
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
}