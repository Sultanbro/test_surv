<?php

namespace App\Service\Payment\Core;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\PriceConvertor;

trait HasPriceConverter
{
    private function getPrice(CreateInvoiceDTO $data): PriceConvertor
    {
        $tariff = Tariff::find($data->tariffId);

        return $tariff
            ->getPriceV2($data->extraUsersLimit, $data->currency);
    }
}