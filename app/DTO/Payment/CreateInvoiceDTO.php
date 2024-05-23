<?php
declare(strict_types=1);

namespace App\DTO\Payment;

final class CreateInvoiceDTO
{
    /**
     * @param string $currency
     * @param float $price
     * @param string $description
     * @param int $quantity
     * @param string|null $expiate_at
     */
    public function __construct(
        public string      $currency,
        public float       $price,
        public string      $description = 'Оплата тарифа',
        public int         $quantity = 1,
        public string|null $expiate_at = null,
    )
    {
    }
}