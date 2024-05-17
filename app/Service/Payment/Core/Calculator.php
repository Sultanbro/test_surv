<?php

namespace App\Service\Payment\Core;

use App\DTO\Payment\NewInvoiceDTO;
use App\Models\Tariff\Tariff;

class Calculator
{
    private array $priceForOnePersonWithCurrencies;

    public function __construct()
    {
        $this->priceForOnePersonWithCurrencies = [
            'kzt' => config('payment.payment_for_one_person_kzt'),
            'rub' => config('payment.payment_for_one_person_rub')
        ];
    }

    public function calc(NewInvoiceDTO $newInvoiceDTO): float
    {
        $tariff = Tariff::find($newInvoiceDTO->tariffId);
        $tariffPrice = $tariff->getPrice($newInvoiceDTO->currency)->value;
        $extraUsersPrice = $this->priceForOnePersonWithCurrencies[$newInvoiceDTO->currency] * $newInvoiceDTO->extraUsersLimit;
        return (float)$tariffPrice + (float)$extraUsersPrice;
    }
}