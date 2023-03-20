<?php
declare(strict_types=1);

namespace App\Service\Bonus;

use App\Service\Bonus\BonusTypes\AllBonusCalculate;
use App\Service\Bonus\BonusTypes\FirstBonusCalculate;
use App\Service\Bonus\BonusTypes\OneBonusCalculate;
use App\Service\Bonus\BonusTypes\PercentageBonusCalculate;
use Exception;

final class BonusManager
{
    /**
     * @param string $type
     * @return BonusCalculator
     * @throws Exception
     */
    public static function call(string $type): BonusCalculator
    {
        return match ($type) {
            'percent'   => new PercentageBonusCalculate(),
            'all'       => new AllBonusCalculate(),
            'one'       => new OneBonusCalculate(),
            'first'     => new FirstBonusCalculate(),

            default => throw new Exception("Invalid bonus type: $type"),
        };
    }
}