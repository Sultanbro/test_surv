<?php

namespace App\Providers;

use BeGateway\GetPaymentToken;
use BeGateway\Logger;
use BeGateway\Settings;
use Illuminate\Support\ServiceProvider;

class ProdamusPaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GetPaymentToken::class, function () {
            Settings::$shopId = config('payment.prodamus.shop_id');
            Settings::$shopKey = config('payment.prodamus.shop_key');
            $transaction = new GetPaymentToken();
            $transaction->setNotificationUrl('http://www.example.com/notify');
            $transaction->setSuccessUrl('http://www.example.com/success');
            $transaction->setDeclineUrl('http://www.example.com/decline');
            $transaction->setFailUrl('http://www.example.com/fail');
            $transaction->setCancelUrl('http://www.example.com/cancel');
            $transaction->setLanguage('ru');
            $transaction->money->setCurrency('RUB');
            return $transaction;
        });
    }
}
