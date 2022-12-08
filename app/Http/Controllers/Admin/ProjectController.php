<?php

namespace App\Http\Controllers\Admin;

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
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {   
        // $subdomain = array_first(explode('.', request()->getHost()));
        if( !$request->has('subdomain') ) {
            return redirect('/');
        } 

        // find tenant
        $tenant = auth()->user()->tenants()->where('id', $request->subdomain)->first();

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

        return $this->loginToSubDomain($tenant, $user->email);
    }

  
}
