<?php

namespace App\Service\Payments\WalletOne;

use App\Models\Tariff\TariffSubscription;
use App\Service\Payments\Core\InvoiceResponse;
use App\Service\Payments\Core\PaymentReport;
use Illuminate\Support\Str;

class WalletOneReport extends PaymentReport
{
    const CURRENCY = ['kzt', 'usd'];

    public function __construct(private readonly array $data)
    {
    }

    public function handle(): InvoiceResponse
    {
        $paymentId = $this->data['WMI_PAYMENT_NO'];
        $status = Str::lower($this->data['WMI_ORDER_STATE']);

        /** @var TariffSubscription $payment */
        $payment = TariffSubscription::query()->where('payment_id', $paymentId)->first();

        if ($payment->payment_id == $paymentId && $status == "accepted") {
            $payment->updateStatusToSuccess();
            return new InvoiceResponse(['WMI_RESULT' => 'OK']);
        }

        return new InvoiceResponse(['WMI_RESULT' => 'RETRY']);
    }
}