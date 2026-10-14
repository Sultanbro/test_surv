<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes([
            'middleware' => [
                'web',
                InitializeTenancyBySubdomain::class,
                PreventAccessFromCentralDomains::class,
            ],
        ]);

        require base_path('routes/channels.php');
    }
}
