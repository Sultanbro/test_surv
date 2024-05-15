<?php

namespace App\Service\Payment\Core;

use App\Models\Tariff\TariffSubscription;

interface AutoPayment
{
    public function makeAutoPayment(TariffSubscription $tariffPayment): void;
}