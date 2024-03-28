<?php
declare(strict_types=1);

namespace App\Service\Payments\Prodamus;

use App\Service\Payments\Core\BasePaymentService;
use App\Service\Payments\Core\PaymentStatus;
use App\Service\Payments\Core\PaymentConnector;
use BeGateway\GetPaymentToken;
use BeGateway\QueryByPaymentToken;

class Prodamus extends BasePaymentService
{

    public function __construct(private ?GetPaymentToken $client = null)
    {
        $this->client = $this->client ?: app(GetPaymentToken::class);
    }

    /**
     * @return PaymentConnector
     */
    public function connector(): PaymentConnector
    {
        return new ProdamusConnector($this->client);
    }

    /**
     * @param string $paymentId
     * @return PaymentStatus
     */
    public function info(string $paymentId): PaymentStatus
    {
        return new ProdamusPaymentStatus($paymentId, new QueryByPaymentToken());
    }
}