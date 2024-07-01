<?php

namespace App\Listeners\Payment\Subscription;

use App\Facade\Payment\Gateway;
use App\Service\Payment\Core\Webhook\BaseWebhookMapper;
use App\Service\Payment\Core\Webhook\WebhookDto;

trait HasPaymentWebhookHandler
{
    public function handler(WebhookDto $dto): BaseWebhookMapper
    {
        $webhookHandler = Gateway::provider($dto->currency)->webhookHandler();
        $webhookHandler->map([
            'params' => $dto->payload,
            'headers' => $dto->headers,
        ]);
        return $webhookHandler;
    }
}