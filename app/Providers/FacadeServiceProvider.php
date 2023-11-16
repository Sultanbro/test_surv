<?php

namespace App\Providers;

use App\Service\Referral\Core\GeneratorInterface;
use App\Service\Referral\UrlGeneratorService;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('referral', function ($app) {
            return new UrlGeneratorService(
                $app->make(GeneratorInterface::class),
            );
        });
    }
}
