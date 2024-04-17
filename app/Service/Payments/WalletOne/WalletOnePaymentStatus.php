<?php

namespace App\Service\Payments\WalletOne;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\PaymentStatus;

class WalletOnePaymentStatus implements PaymentStatus
{
    public function __construct(
        private readonly string $paymentId
    )
    {
    }

    public function getPaymentStatus(): string
    {
        return TariffPayment::getStatus($this->paymentId);
    }
}