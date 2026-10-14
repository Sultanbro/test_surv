<?php

namespace App\Service\Premium\Handlers;

use App\Service\Interfaces\Premium\PremiumTypeInterface;
use App\Service\Premium\PremiumType;
use App\Service\Premium\Types\KpiPremiumType;

class KpiPremiumHandler extends PremiumType
{
    public function getType(
        int $userId,
        string $amount,
        string $comment,
        string $date): PremiumTypeInterface
    {
        return new KpiPremiumType($userId, $amount, $comment, $date);
    }
}