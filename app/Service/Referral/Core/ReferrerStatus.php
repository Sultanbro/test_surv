<?php

namespace App\Service\Referral\Core;

use Exception;
use Illuminate\Support\Arr;
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

    /**
     * @throws Exception
     */
    public static function getPercent(string $status): int
    {
        $statuses = Arr::map(self::cases(), fn(ReferrerStatus $status) => $status->serialize());
        return match (true) {
            $status == $statuses[0] => 0,
            $status == $statuses[1] => 10,
            $status == $statuses[2] => 12,
            default => throw new Exception('Unexpected match value'),
        };
    }

    public function serialize(): string
    {
        return Str::lower($this->name);
    }
}
