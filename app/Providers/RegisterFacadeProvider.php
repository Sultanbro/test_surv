<?php

namespace App\Providers;

use App\Facade\TopValue\TopValue;
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
            return new TopValue();
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
