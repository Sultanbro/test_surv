<?php

namespace App\Service\Payments\Core;

use App\Models\Tariff\PaymentToken;
use JsonSerializable;

class Invoice implements JsonSerializable
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

    public function getPaymentToken(): PaymentToken
    {
        return new PaymentToken($this->paymentId);
    }

    public function getIsSuccess(): string
    {
        return $this->success;
    }

    public function jsonSerialize(): array
    {
        return [
            'payment_id' => $this->getPaymentToken()->token,
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