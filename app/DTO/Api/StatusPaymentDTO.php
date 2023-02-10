<?php
declare(strict_types=1);

namespace App\DTO\Api;

final class StatusPaymentDTO
{
    /**
     * @param string $paymentId
     * @param string $paymentType
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param bool $autoPayment
     */
    public function __construct(
        public string $paymentId,
        public string $paymentType,
        public int $tariffId,
        public int $extraUsersLimit,
        public bool $autoPayment
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'payment_id'    => $this->paymentId,
            'payment_type'  => $this->paymentType,
            'tariff_id'     => $this->tariffId,
            'extra_users_limit' => $this->extraUsersLimit,
            'auto_payment'  => $this->autoPayment
        ];
    }
}