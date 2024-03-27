<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\YooKassaConnectors\YooKassa;

final class PaymentFactory
{
    /**
     * @param string $currency
     * @return BasePaymentService
     */
    public function getPaymentsProviderByCurrency(string $currency): BasePaymentService
    {
        return match ($currency) {
            'rub' => new YooKassa(),
            default => throw new \InvalidArgumentException("Не известная валюта $currency"),
        };
    }

    /**
     * @param string $type
     * @return BasePaymentService
     */
    public function getPaymentsProviderByType(string $type): BasePaymentService
    {
        return match ($type) {
            'yookassa' => new YooKassa(),
            default => throw new \InvalidArgumentException("Не известный тип провайдера $type"),
        };
    }

    /**
     * @param string $paymentId
     * @return BasePaymentService
     */
    public function getPaymentProviderByPaymentId(
        string $paymentId
    ): BasePaymentService
    {
        /**@var TariffPayment $tariffPayment */
        $tariffPayment = TariffPayment::query()
            ->where('payment_id', $paymentId)
            ->firstOrFail();

        return $this->getPaymentProviderByPayment($tariffPayment);
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