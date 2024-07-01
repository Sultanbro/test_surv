<?php

namespace App\Service\Bonus;

use App\Contracts\BonusInterface;
use App\Repositories\Bonus\TestBonusRepository;
use App\User;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
class TestBonusService implements BonusInterface
{
    public function __construct(
        public TestBonusRepository $testBonusRepository
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
        return  $this->testBonusRepository->getUserTestBonuses($date->month, $date->year, $user->id);
    }

}