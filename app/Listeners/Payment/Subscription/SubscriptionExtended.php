<?php

namespace App\Listeners\Payment\Subscription;

use App\Events\Payment\ExtendSubscription;
use App\Facade\Payment\Gateway;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;

class SubscriptionExtended
{
    use HasPaymentWebhookHandler;

    public function handle(ExtendSubscription $event): void
    {
        $dto = $event->dto;
        $webhookHandler = $this->handler($dto);

        if (!$webhookHandler->InvoiceSuccessfullyHandled()) return;
        $invoice = Invoice::getByTransactionId($webhookHandler->getTransactionId());

        if (!$invoice) return;
        if (!$invoice->type->isSubscriptionExtend()) return;


        /** @var TariffSubscription $subscription */
        $subscription = TariffSubscription::query()->where('id', $invoice->payload['subscription_id'])
            ->latest()
            ->first();

        info('Payment provider callback', [
            'params' => $dto->payload,
            'currency' => $dto->currency,
            'invoice_id' => $invoice->id
        ]);

        $invoice->updateStatusToSuccess();
        $subscription->setExtraUsersLimit($invoice->payload['extra_user_limit']);
        $subscription->setExpiredAt($invoice->payload['expired_at']);
    }
}
