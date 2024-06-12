<?php

namespace App\Listeners\Payment;

use App\Events\Payment\PaymentWebhookTriggeredEvent;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;
use App\Service\Invoice\UpdateDealInBitrix;

class UpdateInvoiceStatusInLeadListener
{
    public function handle(PaymentWebhookTriggeredEvent $event): void
    {
        $dto = $event->dto;

        $webhookHandler = Gateway::provider('prodamus')->webhookHandler();
        $webhookHandler->map([
            'params' => $dto->payload,
            'headers' => $dto->headers,
        ]);
        if (!$webhookHandler->InvoiceSuccessfullyHandled()) return;
        $invoice = Invoice::getByPayerPhone($webhookHandler->getParams('customer_phone'));

        $updateLeadService = new UpdateDealInBitrix($invoice);
        $updateLeadService->send();
    }}
