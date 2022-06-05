<?php

namespace App\Providers;

use App\Auth\CustomUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\User;
use App\ProfileGroup;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\ProfileGroup::class => \App\Policies\ProfileGroupPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

       
        // Gate::before(function ($user, $ability) {
        //     return $user->is_admin == 1366  ? true : null;
        // });


        // Gate::define('partner', function ($user) {
        //     $partner = Partner::where('user_id', $user->id)->first();
        //     if ($partner === null) {
        //         return false;
        //     }

        //     return $partner->is_active;
        // });

	    // // Binding eloquent.admin to our EloquentAdminUserProvider
	    // Auth::provider('eloquent.custom', function($app, array $config) {
		//     return new CustomUserProvider($app['hash'], $config['model']);
	    // });

    }
}
