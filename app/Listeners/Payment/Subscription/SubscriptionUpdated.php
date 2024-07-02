<?php

namespace App\Listeners\Payment\Subscription;

use App\Events\Payment\UpdateSubscription;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;

class SubscriptionUpdated
{
    use HasPaymentWebhookHandler;

    public function handle(UpdateSubscription $event): void
    {
        $dto = $event->dto;
        $webhookHandler = $this->handler($dto);

        if (!$webhookHandler->InvoiceSuccessfullyHandled()) return;
        $invoice = Invoice::getByTransactionId($webhookHandler->getTransactionId());

        if (!$invoice) return;
        if (!$invoice->type->isSubscriptionUpdate()) return;


        /** @var TariffSubscription $subscription */
        $subscription = TariffSubscription::query()->find($invoice->payload['subscription_id']);

        info('Payment provider callback', [
            'params' => $dto->payload,
            'currency' => $dto->currency,
            'invoice_id' => $invoice->id
        ]);

        $invoice->updateStatusToSuccess();
        $subscription->setExtraUsersLimit($invoice->payload['extra_user_limit']);
    }
}
