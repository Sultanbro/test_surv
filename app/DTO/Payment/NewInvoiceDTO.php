<?php
declare(strict_types=1);

namespace App\DTO\Payment;

final class NewInvoiceDTO
{
    /**
     * @param string $currency
     * @param int $tariffId
     * @param string $tenantId
     * @param int $extraUsersLimit
     * @param string $provider
     * @param string|null $expiate_at
     */
    public function __construct(
        public string      $currency,
        public int         $tariffId,
        public string      $tenantId,
        public int         $extraUsersLimit = 0,
        public string      $provider = 'prodamus',
        public string|null $expiate_at = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'tariff_id' => $this->tariffId,
            'tenant_id' => $this->tenantId,
            'extra_users_limit' => $this->extraUsersLimit,
            'provider' => $this->provider,
            'expiate_at' => $this->expiate_at,
        ];
    }
}