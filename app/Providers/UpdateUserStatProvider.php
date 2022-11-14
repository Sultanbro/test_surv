<?php

namespace App\Providers;

use App\Models\Analytics\UpdatedUserStat;
use App\Observers\UpdateUserStatObserver;
use Illuminate\Support\ServiceProvider;

class UpdateUserStatProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        UpdatedUserStat::observe(UpdateUserStatObserver::class);
    }
}
