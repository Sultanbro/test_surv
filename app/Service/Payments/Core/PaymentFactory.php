<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Models\Tariff\TariffPayment;
use App\Service\Payments\Prodamus\ProdamusGateway;
use App\Service\Payments\WalletOne\WalletOneGateway;
use InvalidArgumentException;

final class PaymentFactory
{
    /**
     * @param string $currency
     * @return BasePaymentGateway
     */
    public function currencyProvider(string $currency): BasePaymentGateway
    {
        return match ($currency) {
            'rub' => app(ProdamusGateway::class),
            'kzt' => app(WalletOneGateway::class),
            default => throw new InvalidArgumentException("Не известная валюта $currency"),
        };
    }

    /**
     * @param string $type
     * @return BasePaymentGateway
     */
    public function getPaymentsProviderByType(string $type): BasePaymentGateway
    {
        return match ($type) {
            'prodamus' => app(ProdamusGateway::class),
            'wallet1' => app(WalletOneGateway::class),
            default => throw new InvalidArgumentException("Не известный тип провайдера $type"),
        };
    }

    /**
     * @param TariffPayment $tariffPayment
     * @return BasePaymentGateway
     */
    public function getPaymentProviderByPayment(
        TariffPayment $tariffPayment
    ): BasePaymentGateway
    {
        return $this->getPaymentsProviderByType($tariffPayment->service_for_payment);
    }
}