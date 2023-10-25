<?php

namespace App\Providers;

use App\Api\Bitrix\LeadApi;
use App\Api\Bitrix\LeadApiInterface;
use App\Repositories\Referral\EarnedFromReferralRepository;
use App\Repositories\Referral\EarnedFromReferralRepositoryInterface;
use App\Repositories\Referral\StatisticRepository;
use App\Repositories\Referral\StatisticRepositoryInterface;
use App\Repositories\Referral\UserStatisticRepository;
use App\Repositories\Referral\UserStatisticRepositoryInterface;
use App\Service\Referral\Core\Generator;
use App\Service\Referral\Core\GeneratorInterface;
use App\Service\Referral\Core\LeadServiceInterface;
use App\Service\Referral\Core\SalaryCalculator;
use App\Service\Referral\Core\SalaryCalculatorInterface;
use App\Service\Referral\Core\SalaryHandlerInterface;
use App\Service\Referral\Core\SalaryTransaction;
use App\Service\Referral\Core\SalaryTransactionInterface;
use App\Service\Referral\Core\StatusServiceInterface;
use App\Service\Referral\LeadService;
use App\Service\Referral\SalaryHandler;
use App\Service\Referral\StatusService;
use Illuminate\Support\ServiceProvider;

class ReferralServicesProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GeneratorInterface::class, Generator::class);
        $this->app->bind(SalaryCalculatorInterface::class, SalaryCalculator::class);
        $this->app->bind(LeadServiceInterface::class, LeadService::class);
        $this->app->bind(SalaryTransactionInterface::class, SalaryTransaction::class);
        $this->app->bind(SalaryHandlerInterface::class, SalaryHandler::class);
        $this->app->bind(LeadApiInterface::class, LeadApi::class);
        $this->app->bind(EarnedFromReferralRepositoryInterface::class, EarnedFromReferralRepository::class);
        $this->app->bind(StatisticRepositoryInterface::class, StatisticRepository::class);
        $this->app->bind(UserStatisticRepositoryInterface::class, UserStatisticRepository::class);
        $this->app->bind(StatusServiceInterface::class, StatusService::class);
    }
}
