<?php

namespace App\Listeners\Payment\Subscription;

use App\Events\Payment\NewSubscription;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;

class InvoiceStatusReceived
{
    use HasPaymentWebhookHandler;

    public function handle(NewSubscription $event): void
    {
        $dto = $event->dto;
        $webhookHandler = $this->handler($dto);

        if (!$webhookHandler->InvoiceSuccessfullyHandled()) return;
        $invoice = Invoice::getByTransactionId($webhookHandler->getTransactionId());

        if (!$invoice) return;
        if (!$invoice->type->isNewSubscription()) return;


        /** @var TariffSubscription $subscription */
        $subscription = TariffSubscription::query()->find($invoice->payload['subscription_id']);

        info('Payment provider callback', [
            'params' => $dto->payload,
            'currency' => $dto->currency,
            'invoice_id' => $invoice->id
        ]);

        $invoice->updateStatusToSuccess();
        $subscription->updateStatusToSuccess();
    }
}
