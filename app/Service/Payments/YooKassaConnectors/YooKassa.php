<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\Service\Payments\AutoPayment;
use App\Service\Payments\BasePaymentService;
use App\Service\Payments\PaymentStatus;
use App\Service\Payments\PaymentTypeConnector;
use YooKassa\Client;

class YooKassa extends BasePaymentService
{
    /**
     * @var Client
     */
    public Client $client;

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
        $this->merchantId = (int)config('yookassa')['merchant_id'];
        $this->secretKey = (string)config('yookassa')['secret_key'];
        $this->client = new Client();

        $this->client->setAuth($this->merchantId, $this->secretKey);
    }

    /**
     * @return PaymentTypeConnector
     */
    public function getPaymentProvider(): PaymentTypeConnector
    {
        return new YooKassaConnector($this->client);
    }

    /**
     * @param string $paymentId
     * @return PaymentStatus
     */
    public function getPaymentInfo(string $paymentId): PaymentStatus
    {
        return new YooKassaPaymentStatus($this->client, $paymentId);
    }

    /**
     * @return AutoPayment
     */
    public function autoPayment(): AutoPayment
    {
        return new YooKassaAutoPayment($this->client);
    }
}