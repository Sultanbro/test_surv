<?php

namespace App\Traits;

use Exception;
use naffiq\tenge\CurrencyRates;

trait CurrencyTrait
{
    /**
     * @throws Exception
     */
    public function converterToRub(
        float $price
    ): float
    {
        $rates = new CurrencyRates(CurrencyRates::URL_RATES_ALL);
        return $rates->convertFromTenge('RUB', $price);
    }
}