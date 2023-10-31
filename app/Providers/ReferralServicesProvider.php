<?php

namespace App\Providers;

use App\Api\Bitrix\LeadApi;
use App\Api\Bitrix\LeadApiInterface;
use App\Repositories\Referral\StatisticRepository;
use App\Repositories\Referral\StatisticRepositoryInterface;
use App\Repositories\Referral\UserStatisticRepository;
use App\Repositories\Referral\UserStatisticRepositoryInterface;
use App\Service\Referral\CalculatorService;
use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\Generator;
use App\Service\Referral\Core\GeneratorInterface;
use App\Service\Referral\Core\LeadServiceInterface;
use App\Service\Referral\Core\StatusServiceInterface;
use App\Service\Referral\Core\TransactionInterface;
use App\Service\Referral\LeadService;
use App\Service\Referral\StatusService;
use App\Service\Referral\Transaction;
use Illuminate\Support\ServiceProvider;

class ReferralServicesProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GeneratorInterface::class, Generator::class);
        $this->app->bind(LeadServiceInterface::class, LeadService::class);
        $this->app->bind(LeadApiInterface::class, LeadApi::class);
        $this->app->bind(StatusServiceInterface::class, StatusService::class);
        $this->app->bind(StatisticRepositoryInterface::class, StatisticRepository::class);
        $this->app->bind(UserStatisticRepositoryInterface::class, UserStatisticRepository::class);
        $this->app->bind(TransactionInterface::class, Transaction::class);
        $this->app->bind(CalculateInterface::class, CalculatorService::class);
    }
}
