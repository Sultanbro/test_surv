<?php

namespace App\Service\Payments\YooKassaConnectors;

use App\DTO\Api\StatusPaymentDTO;
use App\Enums\Payments\PaymentEnum;
use App\Events\PaymentIsSuccessEvent;
use App\Service\Payments\PaymentStatus;
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

class YooKassaPaymentStatus implements PaymentStatus
{
    /**
     * @var int
     */
    private int $merchantId;

    /**
     * @var string
     */
    private string $secretKey;

    /**
     * @var Client
     */
    private Client $client;

    public function __construct(
        int $merchantId,
        string $secretKey
    )
    {
        $this->merchantId   = $merchantId;
        $this->secretKey    = $secretKey;
        $this->client       = new Client();
    }

    /**
     * @param StatusPaymentDTO $dto
     * @return bool
     * @throws ApiException
     * @throws BadApiRequestException
     * @throws ExtensionNotFoundException
     * @throws ForbiddenException
     * @throws InternalServerError
     * @throws NotFoundException
     * @throws ResponseProcessingException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     */
    public function status(StatusPaymentDTO $dto): bool
    {
        try {
            $this->client->setAuth($this->merchantId, $this->secretKey);
            $paymentStatus = $this->client->getPaymentInfo($dto->paymentId);

            if ($paymentStatus->status == PaymentEnum::STATUS_SUCCESS)
            {
                PaymentIsSuccessEvent::dispatch($dto->tariffId, $dto->extraUsersLimit, $dto->autoPayment);
            }

            return true;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}