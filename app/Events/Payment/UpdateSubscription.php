<?php

namespace App\Events\Payment;

use App\DTO\PaymentEventDTO;
use App\Service\Payment\Core\Webhook\WebhookDto;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateSubscription
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PaymentEventDTO $dto)
    {
    }
}
