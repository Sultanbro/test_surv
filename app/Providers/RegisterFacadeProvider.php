<?php

namespace App\Providers;

use App\Facade\Analytics\Analytics;
use Illuminate\Support\ServiceProvider;

class RegisterFacadeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('top_values', function () {
            return app(Analytics::class);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
