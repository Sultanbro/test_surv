<?php

namespace App\Providers;

use App\Models\Award\Award;
use App\Models\Award\AwardCategory;
use App\Observers\AwardCategoryObserver;
use App\Observers\AwardObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {



    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        AwardCategory::observe(AwardCategoryObserver::class);
        Award::observe(AwardObserver::class);
    }
}
