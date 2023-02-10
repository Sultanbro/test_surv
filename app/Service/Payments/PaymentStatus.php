<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\StatusPaymentDTO;

interface PaymentStatus
{
    public function status(StatusPaymentDTO $dto): bool;
}