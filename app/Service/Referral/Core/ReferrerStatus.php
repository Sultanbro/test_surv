<?php

namespace App\Service\Referral\Core;

use Illuminate\Support\Str;

enum ReferrerStatus: int
{
    case PROMOTER = 0;
    case ACTIVIST = 5;
    case AMBASSADOR = 15;

    public static function fromCount(int $count): ReferrerStatus
    {
        return match (true) {
            $count >= 5 && $count < 15 => self::ACTIVIST,
            $count >= 15 => self::AMBASSADOR,
            default => self::PROMOTER,
        };
    }

    public function serialize(): string
    {
        return Str::lower($this->name);
    }
}
