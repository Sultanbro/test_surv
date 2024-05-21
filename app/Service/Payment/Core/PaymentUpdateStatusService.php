<?php
declare(strict_types=1);

namespace App\Service\Payment\Core;

use App\Facade\Payment\Gateway;
use App\Models\Tariff\TariffSubscription;
use Exception;

final class PaymentUpdateStatusService
{
    /**
     * @param string $tenantId
     * @return bool
     * @throws Exception
     */
    public function handle(string $tenantId): bool
    {
        $lastPayment = TariffSubscription::getLastPendingTariffPayment($tenantId);

        $paymentProvider = Gateway::provider($lastPayment->payment_provider);

        return $paymentProvider->updateStatusByPayment($lastPayment);
    }
}