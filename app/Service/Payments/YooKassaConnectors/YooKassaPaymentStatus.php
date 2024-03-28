<?php

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\StatusPaymentDTO;
use App\Service\Payments\Core\PaymentStatus;
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
     * @param Client $client
     * @param string $paymentId
     */
    public function __construct(
        public Client $client,
        public string $paymentId
    )
    {}

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
    public function getPaymentStatus(): Payment
    {
        return $this->client->getPaymentInfo($this->paymentId);
    }
}