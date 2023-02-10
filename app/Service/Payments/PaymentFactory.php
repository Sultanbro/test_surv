<?php
declare(strict_types=1);

namespace App\Service\Payments;

use App\Service\Payments\YooKassaConnectors\YooKassa;

final class PaymentFactory
{
    /**
     * @param string $currency
     * @return Payment
     */
    public function getPayment(string $currency): Payment
    {
        switch ($currency) {
            case 'rub':
                $factory = new YooKassa();
                break;
        }

        return $factory;
    }
}