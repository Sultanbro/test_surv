<?php

namespace App\Providers;

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
        $this->app->bind(GetPaymentToken::class, function () {
            Settings::$shopId = config('payment.prodamus.shop_id');
            Settings::$shopKey = config('payment.prodamus.shop_key');
            $transaction = new GetPaymentToken();
            $transaction->setTestMode();
            // $this->app->environment('testing')
            $transaction->setSuccessUrl(config('payment.prodamus.success_url'));
            $transaction->setDeclineUrl(config('payment.prodamus.failed_url'));
            $transaction->setFailUrl(config('payment.prodamus.failed_url'));
            $transaction->setCancelUrl(config('payment.prodamus.failed_url'));
            $transaction->setNotificationUrl(route('payment.callback', ['currency' => 'rub']));
            $transaction->setLanguage('ru');
            $transaction->money->setCurrency('RUB');
            return $transaction;
        });

        $this->app->bind(QueryByPaymentToken::class, function () {
            Settings::$shopId = config('payment.prodamus.shop_id');
            Settings::$shopKey = config('payment.prodamus.shop_key');
            return new QueryByPaymentToken();
        });

        $this->app->bind(WalletOne::class, function () {
            $connector = new WalletOneConnector(
                config('payment.wallet1.merchant_id'),
                config('payment.wallet1.success_url'),
                config('payment.wallet1.failed_url')
            // https://{tenant}.jobtron.org/pricing#success
            );
            return new WalletOne($connector);
        });
    }
}
