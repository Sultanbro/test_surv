<?php

namespace App\Listeners;

use App\Enums\Tariff\TariffValidityEnum;
use App\Events\PaymentIsSuccessEvent;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentIsSuccessListener
{
    /**
     * Handle the event.
     *
     * @param PaymentIsSuccessEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(PaymentIsSuccessEvent $event): void
    {
        TariffPayment::createPaymentOrFail(
            $event->tariffId,
            $event->extraUsersLimit,
            Tariff::calculateExpireDate($event->tariffId),
            $event->paymentId,
            $event->paymentType,
            $event->autoPayment
        );
    }
}
