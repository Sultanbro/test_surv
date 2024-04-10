<?php

namespace App\Providers;

use App\Service\Payments\Prodamus\Prodamus;
use App\Service\Payments\Prodamus\ProdamusConnector;
use App\Service\Payments\WalletOne\WalletOne;
use App\Service\Payments\WalletOne\WalletOneConnector;
use BeGateway\GetPaymentToken;
use BeGateway\Logger;
use BeGateway\QueryByPaymentToken;
use BeGateway\Settings;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {

        $this->app->bind(WalletOne::class, function () {
            $connector = new WalletOneConnector(
                config('payment.wallet1.payment_url'),
                config('payment.wallet1.shop_key'),
                config('payment.wallet1.merchant_id'),
                config('payment.wallet1.success_url'),
                config('payment.wallet1.failed_url')
            );
            return new WalletOne($connector);
        });

        $this->app->bind(Prodamus::class, function () {
            $connector = new ProdamusConnector(
                config('payment.prodamus.payment_url'),
                config('payment.prodamus.secret_key'),
                config('payment.prodamus.success_url'),
                config('payment.prodamus.failed_url')
            );
            return new Prodamus($connector);
        });
    }
}
