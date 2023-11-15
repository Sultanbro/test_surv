<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Auth\Traits\CreateTenant;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Controller;
use App\Models\CentralUser;
use App\Models\Tenant;
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
    public function __construct()
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
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        $centralUser = CentralUser::query()->where('email', $user->email);

        $tenant = $this->createTenant($centralUser);

        return response()->json([
            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
        ]);
    }


}
