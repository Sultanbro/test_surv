<?php

namespace App\Events\Payment;

use App\Service\Payment\Core\Webhook\WebhookDto;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentWebhookTriggeredEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public WebhookDto $dto)
    {
    }
}
