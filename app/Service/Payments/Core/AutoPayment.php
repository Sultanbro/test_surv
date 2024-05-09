<?php

namespace App\Service\Payments\Core;

use App\Models\Tariff\TariffSubscription;

interface AutoPayment
{
    public function makeAutoPayment(TariffSubscription $tariffPayment): void;
}