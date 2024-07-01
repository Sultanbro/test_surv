<?php
declare(strict_types=1);

namespace App\DTO\Payment;

final class UpdateInvoiceDTO
{
    /**
     * @param string $tenantId
     * @param int $extraUsersLimit
     */
    public function __construct(
        public string $tenantId,
        public int    $extraUsersLimit = 0,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'extra_users_limit' => $this->extraUsersLimit,
        ];
    }
}