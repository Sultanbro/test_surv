<?php

namespace App\Service\Subscription;

use App\DTO\Payment\NewSubscriptionDTO;

trait CanCalculateTariffPrice
{
    private function getPrice(NewSubscriptionDTO $data): float
    {
        $calculator = new Calculator();

        return $calculator->calc($data);
    }

    public function getPriceForExtraUsers(NewSubscriptionDTO $data): float|int
    {
        $calculator = new Calculator();
        return $calculator->getPriceForExtraUsers($data);
    }
}