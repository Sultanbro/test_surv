<?php

namespace App\Service\Payments;

use App\Models\Tariff\TariffPayment;

interface AutoPayment
{
    public function makeAutoPayment(TariffPayment $tariffPayment): void;
}