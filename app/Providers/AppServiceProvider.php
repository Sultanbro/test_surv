<?php

namespace App\Providers;

use App\User;
use Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Schema;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\HttpFoundation\Response as HttpFoundation;
use Validator;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');

        Schema::defaultStringLength(125);

        $this->setS3DiskForTenant();

        $this->registerMacros();

        // наверное нужно удалить если перешли на layouts.spa

        View::composer('layouts.app', function ($view) {
            dd($view);
            $view->with(['laravelToVue' => $this->dataToVue()]);
        });
        View::composer('layouts.spa', function ($view) {
            $view->with(['laravelToVue' => $this->dataToVue()]);
        });
        View::composer('home', function ($view) {
            $view->with(['laravelToVue' => $this->dataToHomeVue()]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    private function registerMacros(): void
    {
        Response::macro('success', function (mixed $data, int $statusCode = HttpFoundation::HTTP_OK, string $message = 'success') {
            return response()->json([
                'status' => $statusCode,
                'message' => $message,
                'data' => $data
            ]);
        });

        Response::macro('error', function ($message, $status_code) {
            return response()->json([
                'status' => $status_code,
                'message' => $message
            ]);
        });
    }

    private function dataToHomeVue(): array
    {
        if (Auth::guest()) return ['csrfToken' => csrf_token()];

        return [
            'csrfToken' => csrf_token(),
            'userId' => auth()->id(),
            'fullname' => auth()->user()->last_name . ' ' . auth()->user()->name,
            'avatar' => 'https://cp.callibro.org/files/img/8.png',
            'email' => auth()->user()->email,
            'cabinets' => auth()->user()->cabinets()->toArray()
        ];
    }

    private function setS3DiskForTenant(): void
    {
        $host = explode('.', request()->getHost());
        if (count($host) === 3) {
            app()['config']->set('filesystems.disks.s3.bucket', 'tenant' . $host[0]);
        }
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    private function dataToVue(): array
    {
        if (Auth::guest()) return ['csrfToken' => csrf_token()];
        if (auth()->id() == 1 && tenant('id') == null) {
            tenancy()->initialize('bp');
        }
        $user = User::query()->where('email', auth()->user()?->email)?->first() ?? auth()->user();
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
//        }
        if (auth()->user()->program_id === 1 && tenant('id') == 'bp') {
            $permissions[] = 'ucalls_view';
        }
        return [
            'csrfToken' => csrf_token(),
            'userId' => auth()->id(),
            'avatar' => isset(auth()->user()->img_url) && !is_null(auth()->user()->img_url) && auth()->user()->img_url !== ''
                ? '/users_img/' . auth()->user()->img_url
                : 'https://cp.callibro.org/files/img/8.png',
            'email' => auth()->user()->email,
            'is_admin' => auth()->user()->is_admin == 1,
            'permissions' => $permissions,
            'tenants' => auth()->user()->tenants()->pluck('id')->toArray(),
            'cabinets' => auth()->user()->cabinets()->toArray()
        ];
    }
}
