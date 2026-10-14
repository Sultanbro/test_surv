<?php

namespace App\Service\Bonus;

use App\Contracts\BonusInterface;
use App\Repositories\Bonus\ObtainedBonusRepository;
use App\User;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
class ObtainedBonusService implements BonusInterface
{

    public function __construct(
        public ObtainedBonusRepository $obtainedBonusRepository
    )
    {
    }

    /**
     * @param int $month
     * @param int $year
     * @param User $user
     * @return array
     */
    public function getUserBonuses(Carbon $date, User $user): array{
        return $this->obtainedBonusRepository->getUserObtainedBonuses($date->month, $date->year, $user->id);
    }
}