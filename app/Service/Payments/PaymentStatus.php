<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\StatusPaymentDTO;
use YooKassa\Model\Payment;

interface PaymentStatus
{
    public function getPaymentInfo(): Payment;
}