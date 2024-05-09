<?php

namespace App\Service\Payments\Core;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPrice;

trait HasPriceConverter
{
    private function getPrice(NewTariffPaymentDTO $data, ?string $currency = null): TariffPrice
    {
        $tariff = Tariff::getTariffById($data->tariffId);

        return $tariff
            ->getPrice($data->extraUsersLimit)
            ->setCurrency($currency ?? $data->currency);
    }
}