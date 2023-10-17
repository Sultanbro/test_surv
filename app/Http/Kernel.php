<?php

namespace App\Http;

use App\Http\Middleware\CheckIsAdminMiddleware;
use App\Http\Middleware\CheckTariff;
use App\Http\Middleware\OnlyBusinessPartnerMiddleware;
use App\Http\Middleware\Portal\IsOwner;
use App\Http\Middleware\UpdateLastSeenTime;
use App\Http\Middleware\V2\Analytics\AnalyticsCached;
use App\Http\Middleware\V2\Analytics\CacheAnalyticsData;
use App\Http\Middleware\V2\Analytics\CheckAnalyticPermission;
use App\Http\Middleware\V2\Analytics\DecompositionCached;
use App\Http\Middleware\V2\Analytics\GroupsAndActivitiesCached;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [

            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\UserActivity::class,
            \App\Http\Middleware\NotificationMiddleware::class,
            \App\Http\Middleware\Admin::class,
            \App\Http\Middleware\CheckPermissions::class,
            \App\Http\Middleware\ActiveUser::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],

        'tenant' => [
            \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
            \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin' => \App\Http\Middleware\Admin::class,
        'adminbook' => \App\Http\Middleware\AdminBook::class,
        'admin.basic.auth' => \App\Http\Middleware\BasicAuth::class,
        'partner' => \App\Http\Middleware\Partner::class,
        'referral' => \App\Http\Middleware\Referral::class,
        'timezone' => \App\Http\Middleware\Timezone::class,
        'superuser' => \App\Http\Middleware\CheckSuperUser::class,
        'admin_subdomain' => \App\Http\Middleware\IsAdminSubDomain::class,
        'not_admin_subdomain' => \App\Http\Middleware\IsNotAdminSubDomain::class,
        'is_admin' => CheckIsAdminMiddleware::class,
        'check_tariff' => CheckTariff::class,
        'owner' => IsOwner::class,
        'analytics_permission' => CheckAnalyticPermission::class,
        'decomposition_cached' => DecompositionCached::class,
        'analytics_cached' => AnalyticsCached::class,
        'groups_activities_cached' => GroupsAndActivitiesCached::class,
        'only_bp' => OnlyBusinessPartnerMiddleware::class
    ];
}
