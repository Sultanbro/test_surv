<?php

namespace App\Service\Payment\WalletOne;

use App\Classes\Helpers\Phone;
use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Service\Payment\Core\Invoice;
use App\Service\Payment\Core\HasIdempotenceKey;
use App\Service\Payment\Core\PaymentConnector;

class WalletOneConnector implements PaymentConnector
{
    use HasIdempotenceKey;

    const CURRENCIES = [
        'kzt' => 398
    ];

    public function __construct(
        private readonly string $paymentUrl,
        private readonly string $shopKey,
        private readonly string $merchantId,
        private readonly string $successUrl,
        private readonly string $failUrl
    )
    {
    }

    public function createNewInvoice(CreateInvoiceDTO $invoice, CustomerDto $customer): Invoice
    {
        $idempotenceKey = $this->generateIdempotenceKey();
        $body = [
            "WMI_MERCHANT_ID" => $this->merchantId,
            "WMI_CUSTOMER_PHONE" => Phone::normalize($customer->phone),
            "WMI_PAYMENT_NO" => $idempotenceKey,
            "WMI_CURRENCY_ID" => "398",
            "WMI_PAYMENT_AMOUNT" => (string)$invoice->price,
            "WMI_DESCRIPTION" => "BASE64:" . base64_encode($invoice->description . ' ' . time()),
            "WMI_SUCCESS_URL" => $this->successUrl,
            "WMI_FAIL_URL" => $this->failUrl,
        ];

        $signature = new Signature($this->shopKey);
        //Добавление параметра WMI_SIGNATURE в словарь параметров формы
        $body["WMI_SIGNATURE"] = $signature->make($body);

        return new Invoice(
            $this->paymentUrl,
            $idempotenceKey,
            $body
        );
    }

    public function getShopKey(): string
    {
        return $this->shopKey;
    }
}