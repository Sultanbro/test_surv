<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\Service\Payments\Payment;
use App\Service\Payments\PaymentTypeConnector;

class YooKassa extends Payment
{
    /**
     * @var int
     */
    private int $merchantId;

    /**
     * @var string
     */
    private string $secretKey;

    public function __construct()
    {
        $this->merchantId   = config('yookassa')['merchant_id'];
        $this->secretKey    = config('yookassa')['secret_key'];
    }

    /**
     * @return PaymentTypeConnector
     */
    public function getPaymentType(): PaymentTypeConnector
    {
        return  new YooKassaConnector($this->merchantId, $this->secretKey);
    }
}