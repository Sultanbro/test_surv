<?php

namespace App\Providers;

use App\Service\Payments\Core\PaymentGatewayRegistry;
use App\Service\Payments\Prodamus\ProdamusGateway;
use App\Service\Payments\Prodamus\ProdamusConnector;
use App\Service\Payments\WalletOne\WalletOneGateway;
use App\Service\Payments\WalletOne\WalletOneConnector;
use BeGateway\GetPaymentToken;
use BeGateway\Logger;
use BeGateway\QueryByPaymentToken;
use BeGateway\Settings;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->bind(WalletOneGateway::class, function () {
            $connector = new WalletOneConnector(
                config('payment.wallet1.payment_url'),
                config('payment.wallet1.shop_key'),
                config('payment.wallet1.merchant_id'),
                config('payment.wallet1.success_url'),
                config('payment.wallet1.failed_url')
            );
            return new WalletOneGateway($connector);
        });

        $this->app->bind(ProdamusGateway::class, function () {
            $connector = new ProdamusConnector(
                config('payment.prodamus.payment_url'),
                config('payment.prodamus.secret_key'),
                config('payment.prodamus.success_url'),
                config('payment.prodamus.failed_url')
            );
            return new ProdamusGateway($connector);
        });

        $this->app->singleton(PaymentGatewayRegistry::class);
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->app->make(PaymentGatewayRegistry::class)
            ->register(["wallet1", "kzt"], function () {
                $connector = new WalletOneConnector(
                    config('payment.wallet1.payment_url'),
                    config('payment.wallet1.shop_key'),
                    config('payment.wallet1.merchant_id'),
                    config('payment.wallet1.success_url'),
                    config('payment.wallet1.failed_url')
                );
                return new WalletOneGateway($connector);
            });

        $this->app->make(PaymentGatewayRegistry::class)
            ->register(["prodamus", "rub"], function () {
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
