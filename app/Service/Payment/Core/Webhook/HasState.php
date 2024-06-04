<?php

namespace App\Service\Payment\Core\Webhook;

trait HasState
{
    public function InvoiceSuccessfullyHandled(): bool
    {
        return $this->success;
    }

    public function getTransactionId(): string|int
    {
        return $this->transactionId;
    }

    public function getParams(string $key = null): string|int|array
    {
        return $this->payload[$key] ?? $this->payload ?? '';
    }
}