<?php

namespace App\Repositories\Interfaces\Bonus;

interface ObtainedBonusInterface
{
    public function getUserObtainedBonuses(int $month, int $year, int $user_id);

}