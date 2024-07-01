<?php

namespace App\Enums\Payments;

enum CurrencyEnum
{
    const KZT = 'kzt';
    const RUB = 'rub';

    public static function provider(string $currency = 'prodamus'): string
    {
        return match (true) {
            $currency == self::KZT => 'wallet1',
            $currency == self::RUB => 'prodamus'
        };
    }
}
