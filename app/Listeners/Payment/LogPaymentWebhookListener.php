<?php

namespace App\Listeners\Payment;

use App\Events\Payment\NewPracticumInvoiceShipped;
use App\Facade\Payment\Gateway;

class LogPaymentWebhookListener
{
    public function handle($event): void
    {
        $gateway = Gateway::provider($event->dto->currency);
        slack(json_encode([
            'provider' => $gateway->name(),
            'payload' => $event->dto->payload
        ]));
    }
}
