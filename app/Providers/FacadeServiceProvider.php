<?php

namespace App\Providers;

use App\Service\Referral\Core\ReferralDeterminationInterface;
use App\Service\Referral\Core\ReferralGeneratorInterface;
use App\Service\Referral\Core\ReferrerSalaryCalculatorInterface;
use App\Service\Referral\ReferralService;
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
            return new ReferralService(
                $app->make(ReferralGeneratorInterface::class),
                $app->make(ReferralDeterminationInterface::class),
                $app->make(ReferrerSalaryCalculatorInterface::class)
            );
        });
    }
}
