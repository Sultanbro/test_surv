<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentWebhookTriggeredEvent;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;

class ExternalPaymentWebhookListener
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
        $invoice = Invoice::getByTransactionId($webhookHandler->getTransactionId());
        if (!$invoice) return;

        slack(json_encode([
            'params' => $dto->payload,
            'currency' => $dto->currency,
            'invoice_id' => $invoice->id
        ]));

        $invoice->updateStatusToSuccess();
    }
}
