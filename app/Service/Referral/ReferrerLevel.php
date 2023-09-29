<?php

namespace App\Service\Referral;

enum ReferrerLevel: int
{
    case FIRST = 10000;

    case SECOND = 5000;
    case THIRTY = 2000;

    public function next(): ReferrerLevel|false
    {
        foreach (self::cases() as $case) {
            if ($case->value < $this->value) return $case;
        }
        return false;
    }
}
