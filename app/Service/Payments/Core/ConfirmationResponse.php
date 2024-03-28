<?php

namespace App\Service\Payments\Core;

class ConfirmationResponse
{
    public function __construct(
        private readonly string $url,
        private readonly string $paymentId,
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

    public function __serialize(): array
    {
        return [
            'payment_id' => $this->getPaymentId(),
            'redirect_url' => $this->getUrl()
        ];
    }
}