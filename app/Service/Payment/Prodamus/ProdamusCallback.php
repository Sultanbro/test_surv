<?php

namespace App\Service\Payment\Prodamus;

use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\Callback\WebhookCallback;
use App\Service\Payment\Core\Callback\WebhookCallbackResponse;
use App\Service\Payment\Core\Hmac;

class ProdamusCallback extends WebhookCallback
{
    const CURRENCY = 'rub';

    public function __construct(
        private readonly string $shopKey,
        private readonly array  $data
    )
    {
    }

    public function handle(): WebhookCallbackResponse
    {
        $paymentId = $this->data['fields']['order_id'];
        $status = $this->data['fields']['payment_status'] ?? null;
        /** @var TariffSubscription $payment */
        $payment = TariffSubscription::query()->where('payment_id', $paymentId)->first();
        if ($payment && $status == 'success') {
            $payment->updateStatusToSuccess();
            return new WebhookCallbackResponse(['success']);
        }
        return new WebhookCallbackResponse(['failed']);
    }

    private function sign(): bool
    {
        $sign = $this->data['headers']['Sign'] ?? null;
        if (!$sign) return false;
        $signature = new Hmac($this->data['headers']['fields'], $this->shopKey);

        return $signature->verify($sign);
    }
}