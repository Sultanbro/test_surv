<?php
declare(strict_types=1);

namespace App\DTO\Api;

final class CreateInvoiceDTO
{
    /**
     * @param string $currency
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param string $tenantId
     * @param string $provider
     */
    public function __construct(
        public string $currency,
        public int    $tariffId,
        public int    $extraUsersLimit,
        public string $tenantId,
        public string $provider = 'prodamus'
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
            'provider' => $this->provider
        ];
    }
}