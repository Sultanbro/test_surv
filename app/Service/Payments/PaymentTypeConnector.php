<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\DoPaymentDTO;
use YooKassa\Request\Payments\CreatePaymentResponse;

interface PaymentTypeConnector
{
    public function doPayment(DoPaymentDTO $dto): ?CreatePaymentResponse;
}