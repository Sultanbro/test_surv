<?php

namespace App\Providers;

use App\Facade\Analytics\Analytics;
use App\Models\Mailing\Mailing;
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
            return new Analytics();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot():void
    {
        //
    }
}
