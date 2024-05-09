<?php

namespace App\Service\Payments\WalletOne;

use App\Service\Payments\Core\BasePaymentGateway;
use App\Service\Payments\Core\PaymentReport;
use App\Service\Payments\Core\PaymentStatus;

class WalletOneGateway extends BasePaymentGateway
{
    public function __construct(
        private readonly WalletOneConnector $connector
    )
    {
    }

    public function connector(): WalletOneConnector
    {
        return $this->connector;
    }

    public function info(string $paymentId): PaymentStatus
    {
        return new WalletOnePaymentStatus($paymentId);
    }

    public function report(array $data): PaymentReport
    {
        return new WalletOneReport($data);
    }
}