<?php

namespace App\Enums\V2\Analytics;

use InvalidArgumentException;

enum TopValueEnum: int
{
    case UTILITY = 1;

    case RENTABILITY = 2;

    public function labels(): string
    {
        return match ($this) {
            self::UTILITY  => 'Полезность',
            self::RENTABILITY  => 'Маржа',
            default => throw new InvalidArgumentException('Undefined value'),
        };
    }
}
