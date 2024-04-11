<?php
declare(strict_types=1);

namespace App\DTO\Api;

final class PaymentDTO
{
    /**
     * @param string $currency
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param string $provider
     */
    public function __construct(
        public string $currency,
        public int    $tariffId,
        public int    $extraUsersLimit,
        public string $provider = 'prodamus'
    )
    {
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'tariff_id' => $this->tariffId,
            'extra_users_limit' => $this->extraUsersLimit,
            'provider' => $this->provider
        ];
    }
}