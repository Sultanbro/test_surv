<?php

namespace App\Service\Payment\WalletOne;

use App\Service\Payment\Core\BasePaymentGateway;
use App\Service\Payment\Core\Callback\WebhookCallback;
use App\Service\Payment\Core\PaymentStatus;

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

    public function webhook(array $data): WebhookCallback
    {
        return new WalletOneCallback($data);
    }

    public function name(): string
    {
        return 'wallet1';
    }


    public function currency(): string
    {
        return 'kzt';
    }
}