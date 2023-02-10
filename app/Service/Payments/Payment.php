<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\DoPaymentDTO;
use App\DTO\Api\StatusPaymentDTO;

abstract class Payment
{
    abstract public function getPaymentType(): PaymentTypeConnector;

    abstract public function getStatus(): PaymentStatus;

    /**
     * @param DoPaymentDTO $dto
     * @return string
     */
    public function pay(DoPaymentDTO $dto): string
    {
        $connector = $this->getPaymentType();

        return $connector->doPayment($dto);
    }

    /**
     * @param StatusPaymentDTO $dto
     * @return bool
     */
    public function status(StatusPaymentDTO $dto): bool
    {
        return $this->getStatus()->status($dto);
    }
}