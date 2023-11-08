<?php

namespace App\Repositories;

use App\Models\Analytics\Activity;
use App\Models\Analytics\Activity as Model;
use App\Repositories\Interfaces\ActivityInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityRepository extends CoreRepository implements ActivityInterface
{
    /**
     * @return Collection<Activity>
     */
    public function getByGroupIdWithTrashed(int $groupId): Collection
    {
        return Activity::withTrashed()->where('group_id', $groupId)->get();
    }

    /**
     * @return string
     */
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
     * @param Model $activity
     * @param int $year
     * @param int $month
     * @return \Illuminate\Database\Eloquent\Model|HasMany|object|null
     */
    public function getDailyPlan(
        Model $activity,
        int   $year,
        int   $month
    )
    {
        return $activity->plans()->where([
            'year' => $year,
            'month' => $month
        ])->first();
    }
}