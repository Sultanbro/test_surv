<?php

namespace App\Repositories\Bonus;

use App\Models\Admin\ObtainedBonus as Model;
use App\Repositories\CoreRepository;
use App\Repositories\Interfaces\Bonus\ObtainedBonusInterface;
use Carbon\Carbon;

/**
* Класс для работы с Repository.
*/
class ObtainedBonusRepository extends CoreRepository implements ObtainedBonusInterface
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass()
    {
        return Model::class;
    }


    /**
     * @param int $month
     * @param int $year
     * @param int $user_id
     * @return array
     */
    public function getUserObtainedBonuses(int $month, int $year, int $user_id): array
    {
        return $this->model()
            ->where('user_id',$user_id)
            ->whereYear('date', $year)
            ->whereMonth('date',$month)
            ->where('amount', '>', 0)
            ->get()
            ->groupBy(function($b) {
                return Carbon::parse($b->date)->format('d');
            })
            ->toArray();
    }
}