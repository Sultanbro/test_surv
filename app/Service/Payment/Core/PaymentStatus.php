<?php
declare(strict_types=1);

namespace App\Service\Payment\Core;

interface PaymentStatus
{
    public function getPaymentStatus(): string;
}