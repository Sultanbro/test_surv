<?php

namespace App\Service\Payments\WalletOne;

use App\Service\Payments\Core\BasePaymentService;
use App\Service\Payments\Core\PaymentInvoice;
use App\Service\Payments\Core\PaymentStatus;

class WalletOne extends BasePaymentService
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

    public function invoice(array $data): PaymentInvoice
    {
        return new WalletOneInvoice($data);
    }
}