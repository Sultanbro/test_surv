<?php

namespace App\Providers;

use App\Service\Payment\Core\Register;
use App\Service\Payment\Prodamus\ProdamusGateway;
use App\Service\Payment\Prodamus\ProdamusConnector;
use App\Service\Payment\WalletOne\WalletOneGateway;
use App\Service\Payment\WalletOne\WalletOneConnector;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Register::class);
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        /** @var Register $registry */
        $registry = $this->app->make(Register::class);

//        $registry->register(["wallet1", "kzt"], function () {
//            $connector = new WalletOneConnector(
//                config('payment.providers.wallet1.payment_url'),
//                config('payment.providers.wallet1.shop_key'),
//                config('payment.providers.wallet1.merchant_id'),
//                config('payment.providers.wallet1.success_url'),
//                config('payment.providers.wallet1.failed_url')
//            );
//            return new WalletOneGateway($connector);
//        });
//        $registry->register(["prodamus", "rub"], function () {
//            $connector = new ProdamusConnector(
//                config('payment.providers.prodamus.payment_url'),
//                config('payment.providers.prodamus.secret_key'),
//                config('payment.providers.prodamus.success_url'),
//                config('payment.providers.prodamus.failed_url')
//            );
//            return new ProdamusGateway($connector);
//        });
    }
}
