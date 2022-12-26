<?php

namespace App\Repositories\Interfaces\Timetrack;

interface SalaryRepositoryInterface
{
    public function getUserBonuses(int $month, int $year, int $user_id);
    public function getUserAdvance(int $month, int $year, int $user_id);
}