<?php

namespace App\ProfileGroup;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

final class ProfileGroupUsersQuery
{
    private Builder $builder;

    function __construct() {
        $this->builder = \DB::table('users');
    }

    public function whereIsTrainee(bool $isTrainee): self
    {
        $this->builder
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', $isTrainee ? 1 : 0);

        return $this;
    }

    /**
     * @param array<int> $positionIds
     */
    public function wherePositionIds(array $positionIds): self
    {
        $this->builder
            ->whereIn('users.position_id', $positionIds);

        return $this;
    }

    /**
     * @param array<int> $positionIds
     */
    public function whereNotPositionIds(array $positionIds): self
    {
        $this->builder
            ->whereNotIn('users.position_id', $positionIds);

        return $this;
    }

    /**
     * @param int|array<int> $groupId
     * @param ?Carbon $date
     */
    public function groupeFilter(
        int|array $groupId,
        ?Carbon $date,
    ): self
    {
        $this->builder
            ->join('group_user', function (JoinClause $join) use ($groupId, $date) {
                if (is_array($groupId)) {
                    $join->whereIn('group_user.group_id', $groupId);
                } else {
                    $join->on('group_user.group_id', '=', DB::raw($groupId));
                }
                $join->on('group_user.user_id', '=', 'users.id');
                if ($date) {
                    $join->whereDate('group_user.from', '>=', $date);
                    $join->where(function ($query) use ($date) {
                        $query->whereNull('group_user.to');
                        $query->orWhereDate('group_user.to', '<=', $date);
                    });
                }
            });

        return $this;
    }

    /**
     * @param int $deleteType 0 - any 1 - normal 2 - deleted
     * @param ?Carbon $date
     */
    public function deletedByMonthFilter(
        int $deleteType,
        ?Carbon $date,
    ): self
    {
        if ($deleteType == 1) {
            $this->builder->whereNull('users.deleted_at');
        }
        else {
            if ($date) {
                $this->builder->where(function (Builder $query) use ($date, $deleteType) {
                    $query->whereDate(
                        'users.deleted_at',
                        '>=',
                        Carbon::createFromDate($date->year, $date->month, 1)
                        ->format('Y-m-d'),
                    );
//                    if ($deleteType == 0) {
//                        $query->orWhereNull('users.deleted_at');
//                    }
                });
            }
            else if ($deleteType == 2) {
                $this->builder->whereNotNull('users.deleted_at');
            }
        }

        return $this;
    }

    /**
     * @return array<int>
     */
    public function getUserIds(): array
    {
        return $this->builder
            ->select(['users.id'])
//            ->groupBy('users.id')
//            ->get()
            ->pluck('id')
            ->toArray();
    }

    /**
     * @return array<int>
     */
    public function getGroupIds(): array
    {
        return $this->builder
            ->select(['group_user.group_id'])
            ->groupBy('group_user.group_id')
            ->get()
            ->pluck('group_id')
            ->toArray();
    }
}
