<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

use App\Facade\Payment\Gateway;
use App\Models\Tariff\TariffPayment;
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
        $lastPayment = TariffPayment::getLastPendingTariffPayment($ownerId);

        $paymentProvider = Gateway::get($lastPayment->service_for_payment);

        return $paymentProvider->updateStatusByPayment($lastPayment);
    }
}