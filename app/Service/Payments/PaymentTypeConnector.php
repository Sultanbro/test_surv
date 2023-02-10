<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\DoPaymentDTO;

interface PaymentTypeConnector
{
    public function doPayment(DoPaymentDTO $dto);
}