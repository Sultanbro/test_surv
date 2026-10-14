<?php

namespace App\Events\Payment;

use App\DTO\PaymentEventDTO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExtendSubscription
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PaymentEventDTO $dto)
    {
    }
}
