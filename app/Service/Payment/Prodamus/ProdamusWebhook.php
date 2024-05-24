<?php

namespace App\Service\Payment\Prodamus;

use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\Webhook\BaseWebhook;
use App\Service\Payment\Core\Webhook\WebhookResponse;

class ProdamusWebhook implements BaseWebhook
{

    public function handle(array $data): WebhookResponse
    {
        $paymentId = $data['fields']['order_id'];
        $status = $data['fields']['payment_status'] ?? null;
        /** @var TariffSubscription $payment */
        $payment = TariffSubscription::query()->where('payment_id', $paymentId)->first();
        if ($payment && $status == 'success') {
            $payment->updateStatusToSuccess();
            return new WebhookResponse(['success']);
        }
        return new WebhookResponse(['failed']);
    }
}