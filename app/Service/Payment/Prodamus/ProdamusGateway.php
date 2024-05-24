<?php
declare(strict_types=1);

namespace App\Service\Payment\Prodamus;

use App\Service\Payment\Core\Base\BasePaymentGateway;
use App\Service\Payment\Core\Base\PaymentConnector;
use App\Service\Payment\Core\Webhook\BaseWebhook;

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

    public function webhook(): BaseWebhook
    {
        return new ProdamusWebhook();
    }

    public function name(): string
    {
        return 'prodamus';
    }

    public function currency(): string
    {
        return 'rub';
    }
}