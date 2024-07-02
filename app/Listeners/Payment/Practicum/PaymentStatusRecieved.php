<?php

namespace App\Listeners\Payment\Practicum;

use App\Events\Payment\NewPracticumInvoiceShipped;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;

class PaymentStatusRecieved
{
    public function handle(NewPracticumInvoiceShipped $event): void
    {
        $dto = $event->dto;

        $webhookHandler = Gateway::provider('prodamus')->webhookHandler();
        $webhookHandler->map([
            'params' => $dto->payload,
            'headers' => $dto->headers,
        ]);
        if (!$webhookHandler->InvoiceSuccessfullyHandled()) return;
        $invoice = Invoice::getByPayerPhone($webhookHandler->getParams('customer_phone'));
        if (!$invoice) return;

        slack(json_encode([
            'params' => $dto->payload,
            'currency' => $dto->currency,
            'invoice_id' => $invoice->id
        ]));

        $invoice->updateStatusToSuccess();
    }
}
