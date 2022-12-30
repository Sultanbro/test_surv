<?php

namespace App\Service\Premium\Handlers;

use App\Service\Interfaces\Premium\PremiumTypeInterface;
use App\Service\Premium\PremiumType;
use App\Service\Premium\Types\BonusPremiumType;

class BonusPremiumHandler extends PremiumType
{
    public function getType(): PremiumTypeInterface
    {
        return new BonusPremiumType();
    }
}