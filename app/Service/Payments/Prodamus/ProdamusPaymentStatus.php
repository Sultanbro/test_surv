<?php

namespace App\Service\Payments\Prodamus;

use App\Enums\Payments\PaymentStatusEnum;
use App\Service\Payments\Core\PaymentStatus;
use BeGateway\PaymentOperation;
use BeGateway\Response;

class ProdamusPaymentStatus implements PaymentStatus
{
    private Response $response;

    /**
     * @param PaymentOperation $client
     * @param string $paymentId
     */
    public function __construct(
        public PaymentOperation $client,
        public string           $paymentId
    )
    {
    }

    public function getPaymentStatus(): string
    {
        $this->client->setTrackingId($this->paymentId);
        $response = $this->client->submit();

        return match (true) {

            $response->isSuccess() => PaymentStatusEnum::STATUS_SUCCESS,
            $response->isError() => PaymentStatusEnum::STATUS_FAIL,
            $response->isValid() => PaymentStatusEnum::STATUS_SUCCESS,
            $response->isPending() => PaymentStatusEnum::STATUS_PENDING,
            $response->isFailed() => PaymentStatusEnum::STATUS_FAILED,
            default => PaymentStatusEnum::STATUS_UNKNOWN
        };
    }
}