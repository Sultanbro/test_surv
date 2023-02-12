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

class YooKassaPaymentStatus implements PaymentStatus
{
    use YooKassaTrait;

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
        return $this->paymentInfoSave($dto);
    }
}