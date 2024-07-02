<?php

namespace App\Service\Payment\WalletOne;

use App\Classes\Helpers\Phone;
use App\DTO\Payment\CreateInvoiceDTO;
use App\Service\Payment\Core\Base\HasIdempotenceKey;
use App\Service\Payment\Core\Base\PaymentConnector;
use App\Service\Payment\Core\Customer\CustomerDto;
use App\Service\Payment\Core\Invoice\InvoiceResponse;

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

    public function newInvoice(CreateInvoiceDTO $invoice, CustomerDto $customer): InvoiceResponse
    {
        $idempotenceKey = $this->generateIdempotenceKey();
        $body = array_filter([
            "WMI_MERCHANT_ID" => $this->merchantId,
            "WMI_PTENABLED" => 'W1KZT',
//            "WMI_PTDISABLED" => 'W1RUB',
            "WMI_CUSTOMER_PHONE" => Phone::normalize($customer->phone),
            "WMI_PAYMENT_NO" => $idempotenceKey,
            "WMI_CURRENCY_ID" => self::CURRENCIES[$invoice->currency],
            "WMI_PAYMENT_AMOUNT" => $invoice->price,
            "WMI_DESCRIPTION" => "BASE64:" . base64_encode($invoice->description),
            "WMI_CUSTOMER_EMAIL" => $customer->email,
            "WMI_ORDER_ITEMS" => json_encode([[
                "Title" => urlencode($invoice->description),
                "Quantity" => $invoice->quantity,
                "UnitPrice" => 150.00,
                "SubTotal" => 450.00,
                "TaxType" => "tax_ru_1",
                "Tax" => 0.00
            ]]),
            "WMI_SUCCESS_URL" => $this->successUrl,
            "WMI_FAIL_URL" => $this->failUrl
        ]);

        $signature = new WalletOneSignature($this->shopKey);
        //Добавление параметра WMI_SIGNATURE в параметров формы
        $body["WMI_SIGNATURE"] = $signature->make($body);

        return new InvoiceResponse(
            $this->paymentUrl,
            $idempotenceKey,
            'rub',
            $body
        );
    }

    public function getShopKey(): string
    {
        return $this->shopKey;
    }
}