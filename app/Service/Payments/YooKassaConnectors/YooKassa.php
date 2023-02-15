<?php
declare(strict_types=1);

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\StatusPaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\Service\Payments\AutoPayment;
use App\Service\Payments\BasePaymentService;
use App\Service\Payments\Payment;
use App\Service\Payments\PaymentStatus;
use App\Service\Payments\PaymentTypeConnector;
use App\Support\Core\CustomException;
use App\User;
use Exception;
use naffiq\tenge\CurrencyRates;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;
use YooKassa\Common\Exceptions\BadApiRequestException;
use YooKassa\Common\Exceptions\ExtensionNotFoundException;
use YooKassa\Common\Exceptions\ForbiddenException;
use YooKassa\Common\Exceptions\InternalServerError;
use YooKassa\Common\Exceptions\NotFoundException;
use YooKassa\Common\Exceptions\ResponseProcessingException;
use YooKassa\Common\Exceptions\TooManyRequestsException;
use YooKassa\Common\Exceptions\UnauthorizedException;

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
        $this->merchantId   = (int) env('YOOKASSA_MERCHANT_ID');
        $this->secretKey    = (string) env('YOOKASSA_SECRET_KEY');
        $this->client       = new Client();

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