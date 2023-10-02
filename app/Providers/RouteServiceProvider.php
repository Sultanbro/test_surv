<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';


    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
       // $this->configureRateLimiting();

        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

    }


    protected function mapWebRoutes()
    {
        foreach ($this->centralDomains() as $domain) {
            Route::middleware('web')
                ->domain($domain)
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        }
    }

    protected function mapApiRoutes()
    {
        foreach ($this->centralDomains() as $domain) {
            Route::prefix('api')
                ->domain($domain)
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
        }
    }

    protected function centralDomains(): array
    {
        return config('tenancy.central_domains');
    }
}
