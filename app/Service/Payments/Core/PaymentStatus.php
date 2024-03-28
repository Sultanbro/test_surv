<?php
declare(strict_types=1);

namespace App\Service\Payments\Core;

interface PaymentStatus
{
    public function getPaymentStatus(): string;
}