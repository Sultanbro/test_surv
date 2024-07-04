<?php

namespace App\Service\Payment\WalletOne;

use App\Service\Payment\Core\Base\BasePaymentGateway;
use App\Service\Payment\Core\Status\BasePaymentStatus;
use App\Service\Payment\Core\Webhook\BaseWebhookMapper;
use App\Service\Payment\Core\Webhook\WebhookResponse;

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

    public function webhookHandler(): WalletOneWebhookMapper
    {
        return new WalletOneWebhookMapper();
    }

    public function staticWebhookResponse(): WebhookResponse
    {
        return new WebhookResponse(['WMI_RESULT' => 'OK']);
    }

    public function statusManager(): WalletOneStatus
    {
        return new WalletOneStatus($this);
    }
}