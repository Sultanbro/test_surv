<?php

namespace App\Facade\Payment;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Service\Payments\Core\BasePaymentGateway;
use App\Service\Payments\Core\ConfirmationResponse;
use App\Service\Payments\Core\PaymentGatewayRegistry;
use Closure;
use Illuminate\Support\Facades\Facade;
use Mockery;

/**
 * @method static PaymentGatewayRegistry register(string|array $aliases, BasePaymentGateway|Closure $gateway)
 * @method static BasePaymentGateway get(string $name)
 * @method static array list()
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

    public static function fake(): BasePaymentGateway
    {
        $mock = Mockery::mock(BasePaymentGateway::class);
        $mock->shouldReceive("pay")
            ->with([
                new NewTariffPaymentDTO(
                    'test',
                    1,
                    1
                ),4
            ])
            ->once()
            ->andReturn(new ConfirmationResponse(
                'some url',
                'some payment token'
            ));
        Gateway::register(["test"], $mock);
        return Gateway::get("test");
    }
}