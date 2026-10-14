<?php

namespace App\Providers;

use App\Service\Salary\UpdateSalaryInterface;
use App\Service\Salary\UpdateSalaryServiceBetweenRange;
use Illuminate\Support\ServiceProvider;

class SalaryServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(UpdateSalaryInterface::class, UpdateSalaryServiceBetweenRange::class);
//        $this->app->bind(UpdateSalaryInterface::class, UpdateSalaryServicePerDay::class);
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
