<?php

namespace App\Service\Payment\Core\Webhook;

use App\Models\Tariff\Transaction;
use JsonSerializable;

class Invoice implements JsonSerializable
{
    public function __construct(
        private readonly string $url,
        private readonly string $transaction_id,
        private readonly string $currency,
        private readonly array  $params = []
    )
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTransaction(): Transaction
    {
        return new Transaction($this->transaction_id);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function jsonSerialize(): array
    {
        return [
            'payment_id' => $this->getTransaction()->id,
            'url' => $this->getUrl(),
            'params' => $this->getParams()
        ];
    }

    public function getParams(): array
    {
        return $this->params;
    }
}