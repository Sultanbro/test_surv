<?php

namespace App\Classes\Enums;

trait BaseEnum
{
    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}