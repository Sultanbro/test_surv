<?php

namespace App\Service\Payment\Core;

use App\DTO\Payment\NewSubscriptionDTO;

trait CanCalculateTariffPrice
{
    private function getPrice(NewSubscriptionDTO $data): float
    {
        $calculator = new Calculator();

        return $calculator->calc($data);
    }
}