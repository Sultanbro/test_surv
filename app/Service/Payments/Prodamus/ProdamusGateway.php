<?php
declare(strict_types=1);

namespace App\Service\Payments\Prodamus;

use App\Service\Payments\Core\BasePaymentGateway;
use App\Service\Payments\Core\PaymentReport;
use App\Service\Payments\Core\PaymentStatus;
use App\Service\Payments\Core\PaymentConnector;

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

    public function report(array $data): PaymentReport
    {
        return new ProdamusReport($this->connector->getShopKey(), $data);
    }
}