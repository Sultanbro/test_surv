<?php

namespace App\Listeners;

use App\Enums\Tariff\TariffValidityEnum;
use App\Events\PaymentIsSuccessEvent;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PaymentIsSuccessListener
{
    /**
     * Handle the event.
     *
     * @param PaymentIsSuccessEvent $event
     * @return void
     * @throws \Exception
     */
    public function handle(PaymentIsSuccessEvent $event): void
    {
        TariffPayment::createPaymentOrFail(
            $event->tariffId,
            $event->extraUsersLimit,
            $this->calculateExpireDate($event->tariffId),
            $event->autoPayment
        );
    }

    /**
     * @param int $tariffId
     * @return string
     */
    private function calculateExpireDate(int $tariffId): string
    {
        $tariff = Tariff::getTariffById($tariffId);
        $date = now()->addMonth();

        if ($tariff->validity == TariffValidityEnum::Annual)
        {
            $date = now()->addYear();
        }

        return $date;
    }
}
