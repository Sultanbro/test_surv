<?php

namespace App\Providers;

use App\Service\Referral\Core\ReferralDetermination;
use App\Service\Referral\Core\ReferralDeterminationInterface;
use App\Service\Referral\Core\ReferralGenerator;
use App\Service\Referral\Core\ReferralGeneratorInterface;
use App\Service\Referral\Core\ReferralLeadService;
use App\Service\Referral\Core\ReferralLeadServiceInterface;
use App\Service\Referral\Core\ReferrerSalaryHandlerInterface;
use App\Service\Referral\Core\ReferrerSalaryCalculator;
use App\Service\Referral\Core\ReferrerSalaryCalculatorInterface;
use App\Service\Referral\Core\ReferrerSalaryTransaction;
use App\Service\Referral\Core\ReferrerSalaryTransactionInterface;
use App\Service\Referral\ReferrerSalaryHandler;
use Illuminate\Support\ServiceProvider;

class CustomServicesProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReferralGeneratorInterface::class, ReferralGenerator::class);
        $this->app->bind(ReferralDeterminationInterface::class, ReferralDetermination::class);
        $this->app->bind(ReferrerSalaryCalculatorInterface::class, ReferrerSalaryCalculator::class);
        $this->app->bind(ReferralLeadServiceInterface::class, ReferralLeadService::class);
        $this->app->bind(ReferrerSalaryTransactionInterface::class, ReferrerSalaryTransaction::class);
        $this->app->bind(ReferrerSalaryHandlerInterface::class, ReferrerSalaryHandler::class);
    }
}
