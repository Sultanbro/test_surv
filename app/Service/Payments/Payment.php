<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\DTO\Api\DoPaymentDTO;

abstract class Payment
{
    abstract public function getPaymentType(): PaymentTypeConnector;

    /**
     * @param DoPaymentDTO $dto
     * @return string
     */
    public function pay(DoPaymentDTO $dto): string
    {
        $connector = $this->getPaymentType();

        return $connector->doPayment($dto);
    }
}