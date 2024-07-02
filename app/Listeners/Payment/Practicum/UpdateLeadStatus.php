<?php

namespace App\Listeners\Payment\Practicum;

use App\Facade\Payment\Gateway;
use App\Models\Invoice;
use App\Service\Invoice\UpdateDealInBitrix;

class UpdateLeadStatus
{
    public function handle($event): void
    {
        $dto = $event->dto;

        $webhookHandler = Gateway::provider('prodamus')->webhookHandler();
        $webhookHandler->map([
            'params' => $dto->payload,
            'headers' => $dto->headers
        ]);

        if (!$webhookHandler->InvoiceSuccessfullyHandled()) return;
        $invoice = Invoice::getByPayerPhone($webhookHandler->getParams('customer_phone'));

        $updateLeadService = new UpdateDealInBitrix($invoice);
        $updateLeadService->send();
    }
}
