<?php

namespace App\Service\Payments\Core;

use App\Api\BitrixOld\Lead\PaymentLead;
use App\Models\CentralUser;
use App\Models\Tariff\TariffPayment;
use Exception;

class SendPaymentLead
{
    public function createPaymentLead(CentralUser $user, TariffPayment $payment): void
    {
        try {
            (new PaymentLead(
                $user,
                $payment,
                tenant('id'),
                null,
            ))
                ->setNeedCallback(false)
                ->publish();
        } catch (Exception) {
            return; //TODO add logs
        }
    }
}