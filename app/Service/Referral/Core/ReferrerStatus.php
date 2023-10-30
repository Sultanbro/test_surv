<?php

namespace App\Service\Referral\Core;

use Illuminate\Support\Str;

enum ReferrerStatus
{
    case PROMOTER;
    case ACTIVIST;
    case AMBASSADOR;

    public static function fromCount(int $count): ReferrerStatus
    {
        return match (true) {
            $count >= 5 && $count < 15 => self::ACTIVIST,
            $count >= 15 => self::AMBASSADOR,
            default => self::PROMOTER,
        };
    }

    public static function getPercent(ReferrerStatus $status): int
    {
        return match ($status) {
            self::PROMOTER => 0,
            self::ACTIVIST => 10,
            self::AMBASSADOR => 12,
        };
    }

    public function serialize(): string
    {
        return Str::lower($this->name);
    }
}
