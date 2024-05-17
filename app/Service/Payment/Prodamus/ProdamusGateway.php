<?php
declare(strict_types=1);

namespace App\Service\Payment\Prodamus;

use App\Service\Payment\Core\BasePaymentGateway;
use App\Service\Payment\Core\WebhookCallback;
use App\Service\Payment\Core\PaymentStatus;
use App\Service\Payment\Core\PaymentConnector;

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

    /**
     * @param string $paymentId
     * @return PaymentStatus
     */
    public function info(string $paymentId): PaymentStatus
    {
        return new ProdamusPaymentStatus($paymentId);
    }

    public function webhook(array $data): WebhookCallback
    {
        return new ProdamusCallback($this->connector->getShopKey(), $data);
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