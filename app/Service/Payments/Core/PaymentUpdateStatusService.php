<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Models\CentralUser;
use App\Models\Tariff\TariffPayment;
use Exception;

final class PaymentUpdateStatusService
{
    /**
     * @param PaymentFactory $factory
     */
    public function __construct(public PaymentFactory $factory)
    {
    }

    /**
     * @param string $ownerId
     * @return bool
     * @throws Exception
     */
    public function handle(string $ownerId): bool
    {
        $lastPayment = TariffPayment::getLastPendingTariffPayment($ownerId);

        $paymentProvider = $this->factory->getPaymentProviderByPayment($lastPayment);

        return $paymentProvider->updateStatusByPayment($lastPayment);
    }
}