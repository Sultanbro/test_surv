<?php

namespace App\Events;

use App\DTO\Api\StatusPaymentDTO;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentIsSuccessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param string $paymentId
     * @param string $paymentType
     * @param bool $autoPayment
     */
    public function __construct(
        public int $tariffId,
        public int $extraUsersLimit,
        public string $paymentId,
        public string $paymentType,
        public bool $autoPayment = false
    )
    {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('channel-name');
    }
}
