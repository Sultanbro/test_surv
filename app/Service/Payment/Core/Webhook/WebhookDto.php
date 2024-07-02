<?php

namespace App\Service\Payment\Core\Webhook;

class WebhookDto
{
    public function __construct(
        public string $currency,
        public array  $payload,
        public array  $headers = []
    )
    {
    }
}