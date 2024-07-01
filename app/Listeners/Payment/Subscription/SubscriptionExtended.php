<?php

namespace App\Listeners\Payment\Subscription;

use App\Events\Payment\NewPracticumInvoiceShipped;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;

class SubscriptionExtended
{
    public function handle(NewPracticumInvoiceShipped $event): void
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
