<?php

namespace App\Service\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Models\Tariff\PaymentToken;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use Exception;

class SubscribeService
{
    public function __construct(
        private readonly CreateInvoiceDTO $dto,
        private readonly PaymentToken     $token,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function subscribe(): TariffSubscription
    {
        $tariff = Tariff::find($this->dto->tariffId);

        return TariffSubscription::new(
            $this->dto->tenantId,
            $this->dto->tariffId,
            $this->dto->extraUsersLimit,
            $tariff->calculateExpireDate(),
            $this->token->token,
            $this->dto->provider
        );
    }
}