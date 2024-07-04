<?php

namespace App\Listeners\Payment\Subscription;

use App\Events\Payment\UpdateSubscription;
use App\Models\Invoice;
use App\Models\Tariff\TariffSubscription;

class SubscriptionUpdated
{

    public function handle(UpdateSubscription $event): void
    {
        $dto = $event->dto;

        if (!$dto->successStatus) return;

        /** @var Invoice $invoice */
        $invoice = Invoice::query()->find($dto->id);

        if (!$invoice) return;
        if (!$dto->eventType->isSubscriptionUpdate()) return;

        /** @var TariffSubscription $subscription */
        $subscription = TariffSubscription::query()->find($invoice->payload['subscription_id']);

        $invoice->updateStatusToSuccess();
        $subscription->setExtraUsersLimit($invoice->payload['extra_user_limit']);
    }
}
