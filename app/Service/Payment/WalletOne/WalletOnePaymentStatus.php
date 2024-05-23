<?php

namespace App\Service\Payment\WalletOne;

use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\PaymentStatus;

class WalletOnePaymentStatus implements PaymentStatus
{
    public function __construct(
        private readonly string $paymentId
    )
    {
    }

    public function getPaymentStatus(): string
    {
        return TariffSubscription::getStatus($this->paymentId);
    }
}