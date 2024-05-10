<?php

namespace App\Service\Payments\Core;

use JsonSerializable;

class ConfirmationResponse implements JsonSerializable
{
    public function __construct(
        private readonly string $url,
        private readonly string $paymentId,
        private readonly array  $params = [],
        private readonly bool   $success = true,
    )
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getIsSuccess(): string
    {
        return $this->success;
    }

    public function jsonSerialize(): array
    {
        return [
            'payment_id' => $this->getPaymentId(),
            'url' => $this->getUrl(),
            'is_success' => $this->getIsSuccess(),
            'params' => $this->getParams()
        ];
    }

    public function getParams(): array
    {
        return $this->params;
    }
}