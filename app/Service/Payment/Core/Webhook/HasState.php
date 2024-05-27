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
}