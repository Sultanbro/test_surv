<?php

namespace App\Service\Payments\Core;

class ConfirmationResponse
{
    public function __construct(
        private readonly string $url,
        private readonly string $paymentId,
        private readonly bool   $success,
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

    public function __serialize(): array
    {
        return [
            'payment_id' => $this->getPaymentId(),
            'redirect_url' => $this->getUrl(),
            'is_success' => $this->getIsSuccess(),
        ];
    }
}