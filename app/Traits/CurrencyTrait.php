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
    public function converterToRub(
        float $price
    ): float
    {
        $rates = new CurrencyRates(CurrencyRates::URL_RATES_ALL);
        return $rates->convertFromTenge('RUB', $price);
    }

    /**
     * @param float $price price in KZT
     * @throws Exception
     */
    public function createMultiCurrencyPrice(
        float $price
    ): array
    {
        //TODO make enum keys
        return [
            'kzt' => $price,
            'rub' => $this->converterToRub($price),
        ];
    }
}