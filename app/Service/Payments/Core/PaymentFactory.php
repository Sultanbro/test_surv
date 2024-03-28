<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Prodamus\Prodamus;
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
            'rub' => new Prodamus(),
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
            'prodamus' => new Prodamus(),
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