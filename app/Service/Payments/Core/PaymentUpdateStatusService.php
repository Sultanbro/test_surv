<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Models\Tariff\TariffPayment;
use App\User;
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
     * @param User $owner
     * @return bool
     * @throws Exception
     */
    public function handle(User $owner): bool
    {
        $lastPayment = TariffPayment::getLastPendingTariffPayment($owner->id);

        $paymentProvider = $this->factory->getPaymentProviderByPayment($lastPayment);

        return $paymentProvider->updateStatusByPayment($lastPayment, $owner);
    }
}