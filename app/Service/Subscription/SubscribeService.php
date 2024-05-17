<?php

namespace App\Service\Subscription;

use App\DTO\Payment\NewSubscriptionDTO;
use App\Models\Tariff\PaymentToken;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use Exception;

class SubscribeService
{
    public function __construct(
        private readonly NewSubscriptionDTO $dto,
        private readonly PaymentToken       $token,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function subscribe(): TariffSubscription
    {
        $tariff = Tariff::find($this->dto->tariffId);
        $expiate_at = $this->dto->expiate_at ?? $tariff->calculateExpireDate();
        return TariffSubscription::new(
            $this->dto->tenantId,
            $this->dto->tariffId,
            $this->dto->extraUsersLimit,
            $expiate_at,
            $this->token->token,
            $this->dto->provider
        );
    }
}