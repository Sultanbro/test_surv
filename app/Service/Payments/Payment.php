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
     */
    public function status(StatusPaymentDTO $dto)
    {
        return $this->getStatus()->status($dto);
    }
}