<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\ProfileGroup::class => \App\Policies\ProfileGroupPolicy::class,
        \App\KnowBase::class => \App\Policies\KnowBasePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $tenant = tenant('id');

        Gate::before(function ($user, $ability) use ($tenant) {
            if($ability == 'ucalls_view') return $user->is_admin == 1 && $tenant == 'bp' ? true : false;
            
            return $user->is_admin == 1  ? true : null;
        });

    }
}
