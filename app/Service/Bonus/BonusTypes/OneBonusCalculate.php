<?php
declare(strict_types=1);

namespace App\Service\Bonus\BonusTypes;

use App\DTO\Analytics\Statistics\UpdateUserStatDTO;
use App\Models\Kpi\Bonus;
use App\Service\Bonus\BonusCalculator;

class OneBonusCalculate implements BonusCalculator
{
    public function calculate(Bonus $bonus, UpdateUserStatDTO $dto): void
    {
        // TODO: Implement calculate() method.
    }
}