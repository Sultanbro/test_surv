<?php

namespace App\Service\Referral\Core;

enum PaidType
{
    case TRAINEE;
    case ATTESTATION;
    case WORK;
    case FIRST_WORK;

    public static function getValue(PaidType $type): int
    {
        return match ($type) {
            self::TRAINEE => 1000,
            self::FIRST_WORK => 10000,
            self::ATTESTATION, self::WORK => 5000,
        };
    }
}