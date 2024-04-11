<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Prodamus\Prodamus;
use App\Service\Payments\WalletOne\WalletOne;
use InvalidArgumentException;

final class PaymentFactory
{
    /**
     * @param string $currency
     * @return BasePaymentService
     */
    public function currencyProvider(string $currency): BasePaymentService
    {
        return match ($currency) {
            'rub' => app(Prodamus::class),
            'zktOrUsd' => app(WalletOne::class),
            default => throw new InvalidArgumentException("Не известная валюта $currency"),
        };
    }

    /**
     * @param string $type
     * @return BasePaymentService
     */
    public function getPaymentsProviderByType(string $type): BasePaymentService
    {
        return match ($type) {
            'prodamus' => app(Prodamus::class),
            'wallet1' => app(WalletOne::class),
            default => throw new InvalidArgumentException("Не известный тип провайдера $type"),
        };
    }

    /**
     * @param TariffPayment $tariffPayment
     * @return BasePaymentService
     */
    public function getPaymentProviderByPayment(
        TariffPayment $tariffPayment
    ): BasePaymentService
    {
        return $this->getPaymentsProviderByType($tariffPayment->service_for_payment);
    }
}