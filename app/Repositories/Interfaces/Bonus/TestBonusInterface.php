<?php

namespace App\Repositories\Interfaces\Bonus;

interface TestBonusInterface
{
    public function getUserTestBonuses(int $month, int $year, int $user_id);

}