<?php

namespace App\Repositories;

use App\Models\Analytics\Activity as Model;
use App\Repositories\Interfaces\ActivityInterface;

class ActivityRepository extends CoreRepository implements ActivityInterface
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model()->find($id);
    }

    /**
     * @param Model $activity
     * @param int $year
     * @param int $month
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getDailyPlan(
        Model $activity,
        int $year,
        int $month
    )
    {
        return $activity->plans()->where([
            'year' => $year,
            'month' => $month
        ])->first();
    }
}