<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Facade\Payment\Gateway;
use App\Models\Tariff\TariffSubscription;
use Exception;

final class PaymentUpdateStatusService
{
    /**
     * @param string $ownerId
     * @return bool
     * @throws Exception
     */
    public function handle(string $ownerId): bool
    {
        $lastPayment = TariffSubscription::getLastPendingTariffPayment($ownerId);

        $paymentProvider = Gateway::provider($lastPayment->payment_driver);

        return $paymentProvider->updateStatusByPayment($lastPayment);
    }
}