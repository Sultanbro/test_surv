<?php

namespace App\Service\Subscription;

use App\DTO\Payment\NewSubscriptionDTO;
use App\Models\PromoCode;
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

    public function calc(NewSubscriptionDTO $newInvoiceDTO): float
    {
        $tariff = Tariff::find($newInvoiceDTO->tariffId);
        $tariffPrice = $tariff->getPrice($newInvoiceDTO->currency)->value;
        $extraUsersPrice = $this->priceForOnePersonWithCurrencies[$newInvoiceDTO->currency] * $newInvoiceDTO->extraUsersLimit;
        $price = (float)$tariffPrice + (float)$extraUsersPrice;

        return subtractPercent($price, $this->promoCodePercent($newInvoiceDTO));
    }

    private function promoCodePercent(NewSubscriptionDTO $newInvoiceDTO): int|string
    {
        $percent = 0;
        if ($newInvoiceDTO->promo_code) {
            $percent = PromoCode::find($newInvoiceDTO->promo_code)->rate;

        }
        return $percent;
    }

    public function getPriceForExtraUsers(NewSubscriptionDTO $newInvoiceDTO): float|int
    {
       return $this->priceForOnePersonWithCurrencies[$newInvoiceDTO->currency] * $newInvoiceDTO->extraUsersLimit;
    }


}