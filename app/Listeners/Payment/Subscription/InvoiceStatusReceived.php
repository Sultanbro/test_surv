<?php

namespace App\Listeners\Payment\Subscription;

use App\Events\Payment\NewSubscription;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;

class InvoiceStatusReceived
{

    public function handle(NewSubscription $event): void
    {
        $dto = $event->dto;

        if (!$dto->successStatus) return;
        $invoice = Invoice::query()->find($dto->id);

        if (!$invoice) return;
        if (!$dto->eventType->isNewSubscription()) return;


        /** @var TariffSubscription $subscription */
        $subscription = TariffSubscription::query()->find($invoice->payload['subscription_id']);

        $invoice->updateStatusToSuccess();
        $subscription->updateStatusToSuccess();
    }
}
