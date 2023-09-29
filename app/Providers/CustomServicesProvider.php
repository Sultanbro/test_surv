<?php

namespace App\Providers;

use App\Service\Referral\ReferralCalculator;
use App\Service\Referral\ReferralCalculatorInterface;
use App\Service\Referral\ReferralDetermination;
use App\Service\Referral\ReferralDeterminationInterface;
use App\Service\Referral\ReferralGenerator;
use App\Service\Referral\ReferralGeneratorInterface;
use Illuminate\Support\ServiceProvider;

class CustomServicesProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReferralGeneratorInterface::class, ReferralGenerator::class);
        $this->app->bind(ReferralDeterminationInterface::class, ReferralDetermination::class);
        $this->app->bind(ReferralCalculatorInterface::class, ReferralCalculator::class);
    }
}
