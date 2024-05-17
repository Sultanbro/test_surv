<?php

namespace App\Service\Payment\WalletOne;

use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\WebhookCallbackResponse;
use App\Service\Payment\Core\WebhookCallback;
use Illuminate\Support\Str;

class WalletOneCallback extends WebhookCallback
{
    const CURRENCY = ['kzt', 'usd'];

    public function __construct(private readonly array $data)
    {
    }

    public function handle(): WebhookCallbackResponse
    {
        $paymentId = $this->data['WMI_PAYMENT_NO'];
        $status = Str::lower($this->data['WMI_ORDER_STATE']);

        /** @var TariffSubscription $payment */
        $payment = TariffSubscription::query()->where('payment_id', $paymentId)->first();

        if ($payment->payment_id == $paymentId && $status == "accepted") {
            $payment->updateStatusToSuccess();
            return new WebhookCallbackResponse(['WMI_RESULT' => 'OK']);
        }

        return new WebhookCallbackResponse(['WMI_RESULT' => 'RETRY']);
    }
}