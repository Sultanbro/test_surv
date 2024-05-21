<?php

namespace App\Providers;

use App\Service\Payment\Core\PaymentGatewayRegistry;
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
        $this->app->singleton(PaymentGatewayRegistry::class);
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        /** @var PaymentGatewayRegistry $registry */
        $registry = $this->app->make(PaymentGatewayRegistry::class);

        $registry->register(["wallet1", "kzt"], function () {
            $connector = new WalletOneConnector(
                config('payment.wallet1.payment_url'),
                config('payment.wallet1.shop_key'),
                config('payment.wallet1.merchant_id'),
                config('payment.wallet1.success_url'),
                config('payment.wallet1.failed_url')
            );
            return new WalletOneGateway($connector);
        });

        $registry->register(["prodamus", "rub"], function () {
            $connector = new ProdamusConnector(
                config('payment.prodamus.payment_url'),
                config('payment.prodamus.secret_key'),
                config('payment.prodamus.success_url'),
                config('payment.prodamus.failed_url')
            );
            return new ProdamusGateway($connector);
        });
    }
}
