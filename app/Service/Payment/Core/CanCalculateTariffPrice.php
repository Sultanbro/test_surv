<?php

namespace App\Service\Payment\Core;

use App\DTO\Payment\NewInvoiceDTO;

trait CanCalculateTariffPrice
{
    private function getPrice(NewInvoiceDTO $data): float
    {
        $calculator = new Calculator();

        return $calculator->calc($data);
    }
}