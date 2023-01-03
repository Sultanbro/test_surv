<?php

namespace App\Service\Premium;

use App\Service\Interfaces\Premium\PremiumTypeInterface;

abstract class PremiumType
{
    abstract public function getType(
        int $userId,
        string $amount,
        string $comment,
        string $date
    ): PremiumTypeInterface;

    public function handle(
        int $userId,
        string $amount,
        string $comment,
        string $date
    )
    {
        return $this->getType($userId, $amount, $comment, $date)->executeType();
    }
}