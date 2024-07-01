<?php

namespace App\Service\Payment\Core\Webhook;

use JsonSerializable;

class WebhookResponse implements JsonSerializable
{
    public function __construct(private readonly array $response)
    {
    }

    public function jsonSerialize(): array
    {
        return $this->response;
    }
}