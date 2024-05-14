<?php

namespace App\Service\Payments\WalletOne;

use App\Classes\Helpers\Phone;
use App\DTO\Api\NewTariffPaymentDTO;
use App\Models\CentralUser;
use App\Service\Payments\Core\ConfirmationResponse;
use App\Service\Payments\Core\HasIdempotenceKey;
use App\Service\Payments\Core\HasPriceConverter;
use App\Service\Payments\Core\PaymentConnector;
use Illuminate\Support\Str;

class WalletOneConnector implements PaymentConnector
{
    use HasIdempotenceKey;
    use HasPriceConverter;

    const CURRENCY = 398;

    public function __construct(
        private readonly string $paymentUrl,
        private readonly string $shopKey,
        private readonly string $merchantId,
        private readonly string $successUrl,
        private readonly string $failUrl
    )
    {
    }

    public function pay(NewTariffPaymentDTO $data, CentralUser $user): ConfirmationResponse
    {
        $price = $this->getPrice($data);
        $idempotenceKey = $this->generateIdempotenceKey();
        $body = [
            "WMI_MERCHANT_ID" => $this->merchantId,
//            "WMI_PTENABLED" => 'W1KZT',
//            "WMI_PTDISABLED" => 'W1RUB',
            "WMI_CUSTOMER_PHONE" => Phone::normalize($user->phone),
            "WMI_PAYMENT_NO" => $idempotenceKey,
            "WMI_CURRENCY_ID" => self::CURRENCY,
            "WMI_PAYMENT_AMOUNT" => $price->getTotal(),
            "WMI_DESCRIPTION" => "BASE64:" . base64_encode('Заказ №' . time()),
            "WMI_CUSTOMER_EMAIL" => $user->email,
            "WMI_ORDER_ITEMS" => json_encode([[
                "Title" => urlencode("Покупка тарифа"),
                "Quantity" => 1,
                "UnitPrice" => 150.00,
                "SubTotal" => 450.00,
                "TaxType" => "tax_ru_1",
                "Tax" => 0.00
            ]]),
            "WMI_SUCCESS_URL" => $this->successUrl,
            "WMI_FAIL_URL" => $this->failUrl,
        ];

        $signature = new Signature($this->shopKey);
        //Добавление параметра WMI_SIGNATURE в словарь параметров формы
        $body["WMI_SIGNATURE"] = $signature->make($body);

        return new ConfirmationResponse(
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