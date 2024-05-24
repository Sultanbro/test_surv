<?php

namespace App\Service\Payment\Core\Webhook;

interface BaseWebhook
{
    public function handle(array $data): WebhookResponse;
}