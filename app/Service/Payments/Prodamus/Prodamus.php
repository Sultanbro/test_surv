<?php
declare(strict_types=1);

namespace App\Service\Payments\Prodamus;

use App\Service\Payments\Core\BasePaymentService;
use App\Service\Payments\Core\PaymentInvoice;
use App\Service\Payments\Core\PaymentStatus;
use App\Service\Payments\Core\PaymentConnector;

class Prodamus extends BasePaymentService
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

    public function invoice(array $data): PaymentInvoice
    {
        return new ProdamusInvoice($this->connector->getShopKey(), $data);
    }
}