<?php

namespace App\Service\Payment\WalletOne;

use App\Models\Tariff\TariffSubscription;
use App\Service\Payment\Core\Webhook\BaseWebhook;
use App\Service\Payment\Core\Webhook\WebhookResponse;
use Illuminate\Support\Str;

class WalletOneWebhook implements BaseWebhook
{

    public function handle(array $data): WebhookResponse
    {
        $paymentId = $data['WMI_PAYMENT_NO'];
        $status = Str::lower($data['WMI_ORDER_STATE']);

        /** @var TariffSubscription $payment */
        $payment = TariffSubscription::query()->where('payment_id', $paymentId)->first();

        if ($payment->payment_id == $paymentId && $status == "accepted") {
            $payment->updateStatusToSuccess();
            return new WebhookResponse(['WMI_RESULT' => 'OK']);
        }

        return new WebhookResponse(['WMI_RESULT' => 'RETRY']);
    }
}