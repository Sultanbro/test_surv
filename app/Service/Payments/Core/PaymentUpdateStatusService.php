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
     * @param CentralUser $owner
     * @return bool
     * @throws Exception
     */
    public function handle(CentralUser $owner): bool
    {
        $lastPayment = TariffPayment::getLastPendingTariffPayment($owner->id);

        $paymentProvider = $this->factory->getPaymentProviderByPayment($lastPayment);

        return $paymentProvider->updateStatusByPayment($lastPayment);
    }
}