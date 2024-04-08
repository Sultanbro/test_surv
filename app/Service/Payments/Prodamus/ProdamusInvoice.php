<?php

namespace App\Service\Payments\Prodamus;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Core\Hmac;
use App\Service\Payments\Core\InvoiceResponse;
use App\Service\Payments\Core\PaymentInvoice;

class ProdamusInvoice extends PaymentInvoice
{
    const CURRENCY = 'rub';

    public function __construct(
        private readonly string $shopKey,
        private readonly array  $data
    )
    {
    }

    public function handle(): InvoiceResponse
    {
        $paymentId = $this->data['order_id'];
        $status = $this->data['payment_status'] ?? null;
        /** @var TariffPayment $payment */
        $payment = TariffPayment::query()->where('payment_id', $paymentId)->first();
        if ($payment && $status == 'success') {
            $payment->updateStatusToSuccess();
            return new InvoiceResponse(['success']);
        }
        return new InvoiceResponse(['failed']);
    }

    private function sign(): bool
    {
        $sign = $this->data['headers']['Sign'] ?? null;
        if (!$sign) return false;
        $signature = new Hmac($this->data['headers']['fields'], $this->shopKey);

        return $signature->verify($sign);
    }
}