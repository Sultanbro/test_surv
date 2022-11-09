<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SavedKpiInterface;
use App\SavedKpi as Model;
use App\User;
use Carbon\Carbon;

class SavedKpiRepository extends CoreRepository implements SavedKpiInterface
{
    protected function getModelClass()
    {
        return Model::class;
    }

    public function getSavedKpiForMonth(User $user, Carbon $date)
    {
        return $this->model()->where('user_id', $user->id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month);
    }
}