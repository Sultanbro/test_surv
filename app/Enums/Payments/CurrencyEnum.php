<?php

namespace App\Enums\Payments;

enum CurrencyEnum
{
    const KZT = 'kzt';
    const RUB = 'rub';
    const USD = 'usd';

    public static function provider(string $currency = 'prodamus'): string
    {
        return match (true) {
            $currency == self::KZT => 'valet1',
            $currency == self::RUB, $currency == self::USD => 'prodamus'
        };
    }
}
