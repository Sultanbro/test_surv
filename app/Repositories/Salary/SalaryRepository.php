<?php

namespace App\Repositories\Salary;

use App\Repositories\CoreRepository;
use App\Repositories\Interfaces\Timetrack\SalaryRepositoryInterface;
use App\Salary as Model;
use Carbon\Carbon;

/**
* Класс для работы с Repository.
*/
class SalaryRepository extends CoreRepository implements SalaryRepositoryInterface
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
    public function getUserBonuses(int $month, int $year, int $user_id): array
    {
        return $this->model()->where('user_id', $user_id)
            ->whereYear('date',  $year)
            ->whereMonth('date', $month)
            ->where('bonus', '!=', 0)
            ->orderBy('id','desc')
            ->get()
            ->groupBy(function($b) {
                return Carbon::parse($b->date)->format('d');
            })
            ->toArray();
    }

    /**
     * @param int $month
     * @param int $year
     * @param int $user_id
     * @return array
     */
    public function getUserAdvance(int $month, int $year, int $user_id): array
    {
        return $this->model()->where('user_id', $user_id)
            ->whereYear('date',  $year)
            ->whereMonth('date', $month)
            ->where('paid', '!=', 0)
            ->orderBy('id','desc')
            ->get()
            ->groupBy(function($b) {
                return Carbon::parse($b->date)->format('d');
            })
            ->toArray();
    }

    /**
     * @param int $userId
     * @param float $bonus
     * @param string $date
     * @return void
     * @throws \Exception
     */
    public function updateUserBonusPerDate(
        int $userId,
        float $bonus,
        string $date
    ): void
    {
        try {
            $this->model()->where('user_id', $userId)
                ->where('date', $date)
                ->update(
                    [
                        'bonus' =>$bonus
                    ]
                );
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}