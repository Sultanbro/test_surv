<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Auth\Traits\CreateTenant;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Controller;
use App\Models\CentralUser;
use App\Models\Tenant;
use App\Service\Tenancy\CabinetService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login to subdomain through UserImpersonate
    | Create Tenant app
    |--------------------------------------------------------------------------
    */
    use LoginToSubDomain, CreateTenant;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(private readonly CabinetService $cabinetService)
    {
        $this->middleware('auth');
    }

    /**
     * Login to another tenant - subdomain
     *
     * @param String $subdomain
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function login(string $subdomain)
    {
        $cabinet = auth()->user()->cabinets()
            ->where('tenant_id', trim(strtolower($subdomain)))
            ->first();

        if (!$cabinet) {
            return redirect('/');
        }

        $tenant = Tenant::findOrFail(strtolower($subdomain));

        return redirect(
            $this->loginToSubDomain($tenant, auth()->user()->email)
        );
    }

    /**
     * Create new project for Owner and redirect to it
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user->working_country) {
            // App/User cannot be null while creating new tenant
            return response()->json([
                'error' => 'Для создания нового кабинета заполните город в настройках профиля'
            ], 400);
        }
        $centralUser = CentralUser::query()->where('email', $user->email)->first();

        $tenant = $this->createTenant($centralUser);

        $user = $this->createTenantUser($tenant, $user->toArray());

        $this->cabinetService->add($tenant->id, $user, true);

        return response()->json([
            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
        ]);
    }


}
