<?php

namespace App\Models\Tariff;

use App\Traits\CurrencyTrait;
use Exception;
use Illuminate\Support\Str;

final class TariffPrice
{

    use CurrencyTrait;

    private float $priceForOnePersonInKzt;

    private static array $currencyMap = [
        'kzt' => [
            'rub' => 'kztToRub',
            'usd' => 'kztToUsd'
        ],

    ];

    public int $tariffPrice;
    public int $extraUsersPrice;
    public int $priceForOnePerson;

    private string $currency = 'kzt';

    /**
     * @param Tariff $tariff
     * @param int $extraUsers
     */
    public function __construct(
        private readonly Tariff $tariff,
        public int              $extraUsers
    )
    {
        $this->priceForOnePersonInKzt = (float)config('payment.payment_for_one_person');
        $this->setKztPrices();
    }

    public function getTotal(): float
    {
        dd($this->priceForOnePersonInKzt);
        return $this->tariffPrice + $this->extraUsersPrice;
    }

    public function setCurrency(string $newCurrency): self
    {
        $newCurrency = Str::lower($newCurrency);
        $oldCurrency = $this->currency;

        if ($newCurrency == $oldCurrency) {
            return $this;
        }

        $this->currency = $newCurrency;

        if ($newCurrency == 'kzt') {
            $this->setKztPrices();
            return $this;
        }

        $convertMethod = (self::$currencyMap[$oldCurrency])[$newCurrency];
        $this->tariffPrice = $this->{$convertMethod}($this->tariffPrice);
        $this->priceForOnePerson = $this->{$convertMethod}($this->priceForOnePerson);
        $this->updateExtraUsersPrice();

        return $this;
    }

    private function setKztPrices(): void
    {
        $this->tariffPrice = $this->tariff->price;
        $this->priceForOnePerson = $this->priceForOnePersonInKzt;
        $this->updateExtraUsersPrice();
    }

    private function updateExtraUsersPrice(): void
    {
        $this->extraUsersPrice = $this->extraUsers * $this->priceForOnePerson;
    }

    /**
     * @throws Exception
     */
    public function kztToRub(float $v): float
    {
        return $this::converterToRub($v);
    }

    /**
     * @throws Exception
     */
    public function kztToUsd(float $v): float
    {
        return $this::converterToUsd($v);
    }
}