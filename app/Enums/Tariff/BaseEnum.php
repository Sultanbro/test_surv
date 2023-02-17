<?php

namespace App\Enums\Tariff;

trait BaseEnum
{
    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}