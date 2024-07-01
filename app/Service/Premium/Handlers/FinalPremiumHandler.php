<?php

namespace App\Service\Premium\Handlers;

use App\Service\Interfaces\Premium\PremiumTypeInterface;
use App\Service\Premium\PremiumType;
use App\Service\Premium\Types\FinalPremiumType;

class FinalPremiumHandler extends PremiumType
{
    public function getType(int $userId, string $amount, string $comment, string $date): PremiumTypeInterface
    {
        return new FinalPremiumType($userId, $amount, $comment, $date);
    }
}