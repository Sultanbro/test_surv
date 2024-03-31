<?php

namespace App\Service\Payments\WalletOne;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\InvoiceResponse;
use App\Service\Payments\Core\PaymentInvoice;

class WalletOneInvoice extends PaymentInvoice
{
    const CURRENCY = ['kzt', 'usd'];

    public function __construct(private readonly array $data)
    {
    }

    public function handle(): InvoiceResponse
    {
        $paymentId = $this->data['WMI_PAYMENT_NO'];
        $status = $this->data['WMI_ORDER_STATE'];

        /** @var TariffPayment $payment */
        $payment = TariffPayment::query()->where('payment_id', $paymentId)->first();

        if ($payment->payment_id == $paymentId && $status == "ACCEPTED") {
            $payment->updateStatusToSuccess();
            return new InvoiceResponse(['WMI_RESULT' => 'OK']);
        }

        return new InvoiceResponse(['WMI_RESULT' => 'RETRY']);
    }
}