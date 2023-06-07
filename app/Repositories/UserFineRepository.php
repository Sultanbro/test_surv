<?php

namespace App\Repositories;

use App\Fine;
use App\UserFine as Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use function Aws\map;
use Illuminate\Database\Eloquent\Builder;

/**
* Класс для работы с Repository.
*/
class UserFineRepository extends CoreRepository
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
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return object
     */
    public function getUserFines(
        int $userId,
        int $year,
        int $month
    ): object
    {
        return $this->model()
        ->select('user_id', 'fine_id', DB::raw('DATE_FORMAT(day, "%d") as day'), 'status', 'created_at', 'updated_at')
        ->where('user_id', $userId)
        ->whereMonth('day', $month)
        ->whereYear('day', $year)
        ->where('status', Fine::STATUS_FIRST)
        ->whereIn('fine_id', [1, 2])
        ->get();
    }

    /**
     * @param int $userId
     * @param int $fineId
     * @param string $day
     * @return Builder
     */
    public function getUserFine(
        int $userId,
        int $fineId,
        string $day,
    ): Builder
    {
        return $this->model()
            ->whereDate('day', $day)
            ->where('user_id', $userId)
            ->where('fine_id', $fineId);
    }
}