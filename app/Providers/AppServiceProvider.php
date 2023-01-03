<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use \Symfony\Component\HttpFoundation\Response as HttpFoundation;
use Illuminate\Support\ServiceProvider;
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
        Paginator::useBootstrap();

        \Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');

        \Schema::defaultStringLength(125);

        $this->registerMacros();

        \View::composer('layouts.app', function($view) {
            $view->with([
                'laravelToVue' => $this->dataToVue()
            ]);
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

        \View::composer('home', function($view) {
            $view->with([
                'laravelToVue' => $this->dataToHomeVue()
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

    private function registerMacros() : void
    {
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

    private function dataToHomeVue() : array
    {
        if(\Auth::guest()) return ['csrfToken' => csrf_token()];

        return [
            'csrfToken'   => csrf_token(),
            'userId'      => auth()->id(),
            'fullname'    => auth()->user()->last_name . ' ' . auth()->user()->name,
            'avatar'      => 'https://cp.callibro.org/files/img/8.png',
            'email'       => auth()->user()->email,
            'cabinets'    => auth()->user()->cabinets()->toArray()
        ];
    }

    private function dataToVue() : array
    {
        if(\Auth::guest()) return ['csrfToken' => csrf_token()];

        $permissions = auth()->user()->getAllPermissions()->pluck('name')->toArray();

        if(auth()->user()->program_id === 1 && tenant('id') == 'bp') {
            $permissions[] = 'ucalls_view';
        }

        return [
            'csrfToken'   => csrf_token(),
            'userId'      => auth()->id(),
            'avatar'      => isset(auth()->user()->img_url) && !is_null(auth()->user()->img_url) && auth()->user()->img_url !== ''
                ? '/users_img/' . auth()->user()->img_url
                : 'https://cp.callibro.org/files/img/8.png',
            'email'       => auth()->user()->email,
            'is_admin'    => auth()->user()->is_admin == 1,
            'permissions' => $permissions,
            'tenants'     => auth()->user()->tenants()->pluck('id')->toArray(),
            'cabinets'    => auth()->user()->cabinets()->toArray()
        ];
    }
}
