<?php

namespace App\Service\Payments\WalletOne;

use App\Classes\Helpers\Phone;
use App\DTO\Api\PaymentDTO;
use App\Models\CentralUser;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPrice;
use App\Service\Payments\Core\ConfirmationResponse;
use App\Service\Payments\Core\PaymentConnector;
use Illuminate\Support\Str;

class WalletOneConnector implements PaymentConnector
{
    const CURRENCIES = [
        'usd' => 840,
        'kzt' => 398
    ];

    public function __construct(
        private readonly string $merchantId,
        private readonly string $successUrl,
        private readonly string $failUrl
    )
    {
    }

    public function pay(PaymentDTO $data, CentralUser $user): ConfirmationResponse
    {
        $price = $this->getPrice($data);
        $idempotenceKey = $this->generateIdempotenceKey();

        return new ConfirmationResponse(
            'https://wl.walletone.com/checkout/checkout/Index',
            $idempotenceKey,
            true,
            [
                "WMI_MERCHANT_ID" => $this->merchantId,
                "WMI_PTENABLED" => 'WalletOne',
                "WMI_CUSTOMER_PHONE" => Phone::normalize($user->phone),
                "WMI_PAYMENT_NO" => $idempotenceKey,
                "WMI_CURRENCY_ID" => self::CURRENCIES[Str::lower($data->currency)],
                "WMI_PAYMENT_AMOUNT" => $price->getTotal(),
                "WMI_DESCRIPTION" => urlencode('Заказ №' . time()),
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
            ],
        );
    }

    private function getPrice(PaymentDTO $data): TariffPrice
    {
        $tariff = Tariff::getTariffById($data->tariffId);

        return $tariff
            ->getPrice($data->extraUsersLimit)
            ->setCurrency($data->currency);
    }

    private function generateIdempotenceKey(): string
    {
        return uniqid('', true);
    }
}