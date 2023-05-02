<?php

namespace App\Providers;

use App\Models\Mailing\Mailing;
use Illuminate\Support\ServiceProvider;

class MailingNotificationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('mailing', function () {
            return new Mailing();
        });
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
