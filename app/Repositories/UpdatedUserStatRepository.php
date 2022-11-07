<?php

namespace App\Repositories;

use App\Models\Analytics\UpdatedUserStat as Model;
use App\Repositories\Interfaces\UpdatedUserStatRepositoryInterface;
use App\User;
use Carbon\Carbon;

class UpdatedUserStatRepository extends CoreRepository implements UpdatedUserStatRepositoryInterface
{
    protected function getModelClass()
    {
        return Model::class;
    }

    public function getUpdatedStatistics(User $user, Carbon $date): int
    {
        return $this->model()->where('user_id', $user->id)->whereYear('date', $date->year)->whereMonth('date', $date->month)->sum('value');
    }
}