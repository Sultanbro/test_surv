<?php

namespace App\Providers;

use App\Service\Sms\ApiClientInterface;
use App\Service\Sms\SmsInterface;
use App\Service\Sms\UCallApiClient;
use App\Service\Sms\UCallSmsService;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ApiClientInterface::class, function () {
            return new UCallApiClient(config('services.u-call.api_key'));
        });
        $this->app->bind(SmsInterface::class, UCallSmsService::class);
    }

}
