<?php

namespace App\Service\Payments\Core;

use App\Models\Tariff\TariffPayment;

interface AutoPayment
{
    public function makeAutoPayment(TariffPayment $tariffPayment): void;
}