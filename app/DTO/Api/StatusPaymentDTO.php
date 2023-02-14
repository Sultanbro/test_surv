<?php
declare(strict_types=1);

namespace App\DTO\Api;

final class StatusPaymentDTO
{
    /**
     * @param string $paymentId
     * @param string $paymentType
     */
    public function __construct(
        public string $paymentId,
        public string $paymentType
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
            'payment_type'  => $this->paymentType
        ];
    }
}