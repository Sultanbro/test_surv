<?php
declare(strict_types=1);

namespace App\DTO\Api;

final class StatusPaymentDTO
{
    /**
     * @param string $paymentId
     */
    public function __construct(
        public string $paymentId
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'payment_id'    => $this->paymentId
        ];
    }
}