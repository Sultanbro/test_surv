<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\DoPaymentDTO;
use YooKassa\Request\Payments\CreatePaymentResponse;
use App\User;

interface PaymentTypeConnector
{
    public function doPayment(DoPaymentDTO $dto, User $authUser): ?CreatePaymentResponse;
}