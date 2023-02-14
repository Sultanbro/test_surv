<?php

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\StatusPaymentDTO;
use App\Enums\ErrorCode;
use App\Enums\Payments\PaymentStatusEnum;
use App\Events\PaymentIsSuccessEvent;
use App\Service\Payments\PaymentStatus;
use App\Support\Core\CustomException;
use App\Traits\YooKassaTrait;
use PHPUnit\Util\Exception;
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
use YooKassa\Model\Payment;

/**
 * @property YooKassa $yookassa
 */
class YooKassaPaymentStatus implements PaymentStatus
{
    /**
     * @var Client
     */
    private Client $client;

    public function __construct(
        public int $merchantId,
        public string $secretKey,
        public string $paymentId
    )
    {
        $this->client = new Client();
        $this->client->setAuth($this->merchantId, $this->secretKey);
    }

    /**
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws ApiException
     * @throws ExtensionNotFoundException
     * @throws BadApiRequestException
     * @throws InternalServerError
     * @throws ForbiddenException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function getPaymentInfo(): Payment
    {
        return $this->client->getPaymentInfo($this->paymentId);
    }
}