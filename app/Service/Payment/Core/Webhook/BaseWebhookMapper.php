<?php

namespace App\Service\Payment\Core\Webhook;

interface BaseWebhookMapper
{
    public function map(array $data): void;

    public function InvoiceSuccessfullyHandled(): bool;

    public function getTransactionId(): string|int;

    public function getParams(string $key = null): string|int|array;
}