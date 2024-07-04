<?php

namespace App\Listeners\Payment\Practicum;

use App\Events\Payment\NewPracticumInvoiceShipped;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;

class PaymentStatusReceived
{
    public function handle(NewPracticumInvoiceShipped $event): void
    {
        $dto = $event->dto;

        if (!$dto->successStatus) return;
        $invoice = Invoice::query()->find($dto->id);
        if (!$invoice) return;

        $invoice->updateStatusToSuccess();
    }
}
