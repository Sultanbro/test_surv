<?php

namespace App\Providers;

use App\Service\Analytics\CreatePivotAnalytics;
use App\Service\Analytics\CreatePivotAnalyticsInterface;
use Illuminate\Support\ServiceProvider;

class AnalyticServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(CreatePivotAnalyticsInterface::class, CreatePivotAnalytics::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
