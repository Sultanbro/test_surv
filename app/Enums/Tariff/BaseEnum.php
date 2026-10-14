<?php

namespace App\Enums\Tariff;

trait BaseEnum
{
    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isInEnum(string $v): bool {
        $values = self::getAllValues();
        return in_array($v, $values);
    }
}
