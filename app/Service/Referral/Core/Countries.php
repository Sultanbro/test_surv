<?php

namespace App\Service\Referral\Core;

enum Countries: string
{
    case KZ = "2330";
    case RU = "2332";
    case KG = "2334";
    case UZ = "2336";
    case UA = "2388";
    case BY = "2390";
    case UN = "0"; // Неизвестно

    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }
}