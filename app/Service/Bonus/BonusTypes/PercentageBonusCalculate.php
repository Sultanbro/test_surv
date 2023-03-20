<?php

namespace App\Service\Bonus\BonusTypes;

use App\DTO\Analytics\Statistics\UpdateUserStatDTO;
use App\Models\Kpi\Bonus;
use App\Repositories\Salary\SalaryRepository;
use App\Salary;
use App\Service\Bonus\BonusCalculator;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class PercentageBonusCalculate implements BonusCalculator
{
    /**
     * @var SalaryRepository
     */
    private SalaryRepository $repository;

    public function __construct()
    {
        $this->repository = new SalaryRepository();
    }

    /**
     * @param Bonus $bonus
     * @param UpdateUserStatDTO $dto
     * @return void
     * @throws Exception
     */
    #[NoReturn] public function calculate(Bonus $bonus, UpdateUserStatDTO $dto): void
    {
        $percent = $bonus->sum / 100;
        $date = $bonus->created_at->format('Y-m-d');

        if ($bonus->quantity <= $dto->value)
        {
            $bonusValue = $dto->value * $percent;
        } else {
            $bonusValue = 0;
        }

        $this->repository->updateUserBonusPerDate($dto->employeeId, $bonusValue, $date);
    }
}