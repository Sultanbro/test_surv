<?php

namespace App\Facade\Payment;

use App\Service\Payments\Core\BasePaymentGateway;
use App\Service\Payments\Core\PaymentGatewayRegistry;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PaymentGatewayRegistry register(string|array $name)
 * @method static BasePaymentGateway get(string $name)
 * @mixin PaymentGatewayRegistry
 */
class Gateway extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'payment_gateway';
    }
}