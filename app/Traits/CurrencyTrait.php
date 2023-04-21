<?php

namespace App\Traits;

use Exception;
use naffiq\tenge\CurrencyRates;

trait CurrencyTrait
{
    /**
     * @param float $price price in KZT
     * @throws Exception
     */
    public static function converterToRub(
        float $price
    ): float
    {
        $rates = new CurrencyRates(CurrencyRates::URL_RATES_ALL, 10);
        return $rates->convertFromTenge('RUB', $price);
    }

    /**
     * @param float $price
     * @return float
     * @throws Exception
     */
    public static function convertToUsd(
        float $price
    ): float
    {
        $rates = new CurrencyRates(CurrencyRates::URL_RATES_ALL, 10);
        return $rates->convertFromTenge('USD', $price);
    }

    /**
     * @param float $price price in KZT
     * @throws Exception
     */
    public static function createMultiCurrencyPrice(
        float $price
    ): array
    {
        //TODO make enum keys
        return [
            'kzt' => $price,
            'rub' => self::converterToRub($price),
            'usd' => self::convertToUsd($price)
        ];
    }
}