<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Auth\Traits\CreateTenant;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Controller;
use App\Models\CentralUser;
use App\Models\Tenant;
use App\Service\Tenancy\CabinetService;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
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
    public function __construct(protected CabinetService $cabinetService)
    {
        $this->middleware('auth');
    }

    /**
     * Login to another tenant - subdomain
     *
     * @param String $subdomain
     * @return \Illuminate\Http\RedirectResponse|JsonResponse
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
     * @return JsonResponse
     * @throws Exception
     */
    public function create(): JsonResponse
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        // deprecated
//        if (!$user->working_country) {
//            // App/User cannot be null while creating new tenant
//            return response()->json([
//                'error' => 'Для создания нового кабинета заполните город в настройках профиля'
//            ], 400);
//        }


        $centralUser = CentralUser::userByEmail($authUser->email);

        $tenant = $this->createTenant($centralUser);
        $password = $centralUser->password;
        $data = $authUser->toArray();
        $data['password'] = $password;
        $user = $this->createTenantUser($tenant, $data);
        $this->cabinetService->add($tenant->id, $user, true);

        return response()->json([
            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
        ]);
    }


}
