<?php

namespace App\Service\Salary;

use App\Repositories\Salary\SalaryRepository;
use App\User;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
class SalaryService
{

    /**
     * @param SalaryRepository $salaryRepository
     */
    public function __construct(
        public SalaryRepository $salaryRepository
    )
    {
    }
    /**
     * @param int $month
     * @param int $year
     * @param User $user
     * @return array
     *
     * Получить бонусы пользователя в таблице salaries
     */
    public function getUserBonuses(Carbon $date, User $user): array
    {
        return $this->salaryRepository->getUserBonuses($date->month, $date->year, $user->id);

    }

    /**
     * @param int $month
     * @param int $year
     * @param User $user
     * @return array
     *
     *
     * Получить все авансы в таблице salaries
     */
    public function getUserAdvances(Carbon $date, User $user): array
    {
        return $this->salaryRepository->getUserAdvance($date->month, $date->year, $user->id);

    }

}