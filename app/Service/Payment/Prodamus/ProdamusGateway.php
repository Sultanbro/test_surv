<?php
declare(strict_types=1);

namespace App\Service\Payment\Prodamus;

use App\Service\Payment\Core\Base\BasePaymentGateway;
use App\Service\Payment\Core\Base\PaymentConnector;
use App\Service\Payment\Core\Webhook\BaseWebhookMapper;
use App\Service\Payment\Core\Webhook\WebhookResponse;
use App\Service\Payment\WalletOne\WalletOneStatus;

class ProdamusGateway extends BasePaymentGateway
{
    public function __construct(
        private readonly ProdamusConnector $connector
    )
    {
    }

    /**
     * @return PaymentConnector
     */
    public function connector(): PaymentConnector
    {
        return $this->connector;
    }

    public function webhookHandler(): BaseWebhookMapper
    {
        return new ProdamusWebhookMapper();
    }

    public function name(): string
    {
        return 'prodamus';
    }

    public function currency(): string
    {
        return 'rub';
    }

    public function staticWebhookResponse(): WebhookResponse
    {
        return new WebhookResponse(['success']);
    }

    public function statusManager(): WalletOneStatus
    {
        return new WalletOneStatus($this);
    }
}