<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentWebhookTriggeredEvent;
use App\Facade\Payment\Gateway;
use App\Models\Tariff\TariffSubscription;

class TariffPaymentWebhookListener
{
    public function handle(PaymentWebhookTriggeredEvent $event): void
    {
        $dto = $event->dto;
        $webhookHandler = Gateway::provider($dto->currency)->webhookHandler();
        $webhookHandler->map([
            'params' => $dto->payload,
            'headers' => $dto->headers,
        ]);

        if (!$webhookHandler->InvoiceSuccessfullyHandled()) return;
        $invoice = TariffSubscription::getByTransactionId($webhookHandler->getTransactionId());
        if (!$invoice) return;
        $invoice->updateStatusToSuccess();
    }
}
