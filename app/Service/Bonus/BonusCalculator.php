<?php

namespace App\Service\Bonus;

use App\DTO\Analytics\Statistics\UpdateUserStatDTO;
use App\Models\Kpi\Bonus;

interface BonusCalculator
{
    public function calculate(Bonus $bonus, UpdateUserStatDTO $dto): void;
}