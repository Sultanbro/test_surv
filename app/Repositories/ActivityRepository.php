<?php

namespace App\Repositories;

use App\Models\Analytics\Activity as Model;
use App\Repositories\Interfaces\ActivityInterface;

class ActivityRepository extends CoreRepository implements ActivityInterface
{
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
    public function getById($id)
    {
        return $this->model()->find($id);
    }

    public function getDailyPlan(
        Model $activity,
        int   $year,
        int   $month
    ): ?Model
    {
        return $activity->plans()->where([
            'year' => $year,
            'month' => $month
        ])->first();
    }
}