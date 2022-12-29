<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Auth\Traits\CreateTenant;

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
    public function login(String $subdomain)
    {   
        // $subdomain = array_first(explode('.', request()->getHost()));
        // Find tenant
        $tenant = auth()->user()->tenants()
            ->where('id', trim( strtolower($subdomain) ))
            ->first();

        // can't login cause tenant not found
        if( !$tenant ) {
            return redirect('/');
        }
        
        // login
        return $this->loginToSubDomain($tenant, auth()->user()->email);
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

        $tenant = $this->createTenant($user);

        return response()->json([
            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
        ]);
    }

  
}
