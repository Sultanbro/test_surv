<?php
declare(strict_types=1);

namespace App\DTO\Api;

final class DoPaymentDTO
{
    /**
     * @param string $currency
     * @param int $tariffId
     * @param int $extraUsersLimit
     */
    public function __construct(
        public string $currency,
        public int $tariffId,
        public int $extraUsersLimit
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'currency'          => $this->currency,
            'tariff_id'         => $this->tariffId,
            'extra_users_limit' => $this->extraUsersLimit
        ];
    }
}