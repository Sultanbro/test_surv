<?php

namespace App\Service\Payment\WalletOne;

use App\Service\Payment\Core\Base\BasePaymentGateway;
use App\Service\Payment\Core\Webhook\BaseWebhook;

class WalletOneGateway extends BasePaymentGateway
{
    public function __construct(
        private readonly WalletOneConnector $connector
    )
    {
    }


    public function name(): string
    {
        return 'wallet1';
    }


    public function currency(): string
    {
        return 'kzt';
    }

    public function connector(): WalletOneConnector
    {
        return $this->connector;
    }

    public function webhook(): BaseWebhook
    {
        return new WalletOneWebhook();
    }
}