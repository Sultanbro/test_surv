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

    public function getDailyPlan(
        Model $activity,
        int $year,
        int $month
    )
    {
        return $activity->plans()->where([
            ['year', $year],
            ['month', $month]
        ])->first();
    }
}