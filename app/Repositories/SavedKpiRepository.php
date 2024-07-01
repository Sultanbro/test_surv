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

    /**
     * Проверяем если есть запись тогда обновляем если нет то создаем новую.
     *
     * @param $user
     * @param $date
     * @param $total
     * @return mixed
     */
    public function updateOrCreate($user, $date, $total)
    {
        $date = Carbon::parse($date)->day(1)->format('Y-m-d');

        return $this->model()->updateOrCreate(
            [
                'date' => $date,
                'user_id' => $user->id
            ],
            [
                'date' => $date,
                'user_id' => $user->id,
                'total' => $total
            ]
        );
    }
}