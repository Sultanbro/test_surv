<?php

namespace App\Providers;

use App\Models\User\Referral\Referral;
use App\Models\User\Referral\Referrer;
use App\Service\Referral\Core\ReferralInterface;
use App\Service\Referral\Core\ReferrerInterface;
use Illuminate\Support\ServiceProvider;

class ModelInterfaceServicesProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ReferralInterface::class, Referral::class);
        $this->app->bind(ReferrerInterface::class, Referrer::class);
    }
}
