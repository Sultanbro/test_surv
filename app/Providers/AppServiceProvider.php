<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use \Symfony\Component\HttpFoundation\Response as HttpFoundation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Notification;
use App\UserNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Instead of Tailwind 
        Paginator::useBootstrap();

        \Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');

        \Schema::defaultStringLength(125);

        \View::composer('layouts.app', function($view) {

            if(!\Auth::guest()) {

                $permissions = auth()->user()->getAllPermissions()->pluck('name')->toArray(); // Spatie permissions

                if(auth()->user()->program_id === 1) {
                    $permissions[] = 'ucalls_view';
                } 

                $view->with([
                    'laravelToVue' => [
                        'csrfToken'   => csrf_token(),
                        'userId'      => auth()->id(),
                        'avatar'      => isset(auth()->user()->img_url) && !is_null(auth()->user()->img_url) && auth()->user()->img_url !== ''
                            ? '/users_img/' . auth()->user()->img_url
                            : 'https://cp.callibro.org/files/img/8.png',
                        'email'       => auth()->user()->email,
                        'is_admin'    => auth()->user()->is_admin == 1,
                        'permissions' => $permissions,
                        'tenants'     => auth()->user()->tenants()->pluck('id')->toArray()
                    ]
                ]);

            } else {
                $view->with([
                    'laravelToVue' => [
                        'csrfToken'   => csrf_token(),
                    ]
                ]);
            }

        });

        \View::composer('layouts.spa', function($view) {

            if(!\Auth::guest()) {

                $permissions = auth()->user()->getAllPermissions()->pluck('name')->toArray(); // Spatie permissions

                if(auth()->user()->program_id === 1) {
                    $permissions[] = 'ucalls_view';
                } 

                $view->with([
                    'laravelToVue' => [
                        'csrfToken'   => csrf_token(),
                        'userId'      => auth()->id(),
                        'avatar'      => isset(auth()->user()->img_url) && !is_null(auth()->user()->img_url) && auth()->user()->img_url !== ''
                            ? '/users_img/' . auth()->user()->img_url
                            : 'https://cp.callibro.org/files/img/8.png',
                        'email'       => auth()->user()->email,
                        'is_admin'    => auth()->user()->is_admin == 1,
                        'permissions' => $permissions,
                        'tenants'     => auth()->user()->tenants()->pluck('id')->toArray()
                    ]
                ]);

            } else {
                $view->with([
                    'laravelToVue' => [
                        'csrfToken'   => csrf_token(),
                    ]
                ]);
            }

        });

        Response::macro('success', function ($data, $statusCode = HttpFoundation::HTTP_OK, $message = 'success',) {
            return response()->json([
                'status'  => $statusCode,
                'message' => $message,
                'data' => $data
            ]);
        });

        Response::macro('error', function ($message, $status_code) {
            return response()->json([
                'status'  => $status_code,
                'message' => $message
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
