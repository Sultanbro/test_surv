<?php

namespace App\Service\Payments\Prodamus;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\InvoiceResponse;
use App\Service\Payments\Core\PaymentInvoice;

class ProdamusInvoice extends PaymentInvoice
{
    const CURRENCY = 'rub';

    public function __construct(private readonly array $data)
    {
    }

    public function handle(): InvoiceResponse
    {
        $paymentId = $this->data['transaction']['tracking_id'];
        $status = $this->data['transaction']['status'];
        /** @var TariffPayment $payment */
        $payment = TariffPayment::query()->where('payment_id', $paymentId)->first();
        if ($payment && $status == 'successful') {
            $payment->updateStatusToSuccess();
            return new InvoiceResponse(['success']);
        }
        return new InvoiceResponse(['failed']);
    }
}