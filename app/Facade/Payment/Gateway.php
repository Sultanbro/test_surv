<?php

namespace App\Facade\Payment;

use App\DTO\Payment\NewSubscriptionDTO;
use App\Service\Payment\Core\Base\BasePaymentGateway;
use App\Service\Payment\Core\Invoice\Invoice;
use App\Service\Payment\Core\Register;
use Closure;
use Illuminate\Support\Facades\Facade;
use Mockery;

/**
 * @method static Register register(string|array $aliases, BasePaymentGateway|Closure $gateway)
 * @method static BasePaymentGateway provider(string $name)
 * @method static array list()
 * @method static bool exists(string $name)
 * @mixin Register
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
        $mock->shouldReceive("createNewInvoice")
            ->with([
                new NewSubscriptionDTO(
                    'test',
                    1,
                    1,
                    1,
                ),
                4
            ])
            ->once()
            ->andReturn(new Invoice(
                'some url',
                'test_id',
                'some payment token'
            ));

        Gateway::register(["test"], $mock);
        return Gateway::provider("test");
    }
}