<?php

namespace App\Service\Payments\Prodamus;

use App\Enums\Payments\PaymentStatusEnum;
use App\Service\Payments\Core\PaymentStatus;
use BeGateway\QueryByPaymentToken;

class ProdamusPaymentStatus implements PaymentStatus
{
    public function __construct(
        private readonly string              $paymentId
    )
    {
    }

    public function getPaymentStatus(): string
    {
        $this->client->setToken($this->paymentId);
        $response = $this->client->submit();

        return match (true) {

            $response->isSuccess() => PaymentStatusEnum::STATUS_SUCCESS,
            $response->isError() => PaymentStatusEnum::STATUS_FAIL,
            $response->isValid() => PaymentStatusEnum::STATUS_SUCCESS,
            default => PaymentStatusEnum::STATUS_UNKNOWN
        };
    }
}