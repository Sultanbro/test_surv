<?php

namespace App\Providers;

use App\Service\Referral\Core\ReferralDetermination;
use App\Service\Referral\Core\ReferralDeterminationInterface;
use App\Service\Referral\Core\ReferralGenerator;
use App\Service\Referral\Core\ReferralGeneratorInterface;
use App\Service\Referral\Core\ReferrerSalaryCalculator;
use App\Service\Referral\Core\ReferrerSalaryCalculatorInterface;
use Illuminate\Support\ServiceProvider;

class CustomServicesProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReferralGeneratorInterface::class, ReferralGenerator::class);
        $this->app->bind(ReferralDeterminationInterface::class, ReferralDetermination::class);
        $this->app->bind(ReferrerSalaryCalculatorInterface::class, ReferrerSalaryCalculator::class);
    }
}
