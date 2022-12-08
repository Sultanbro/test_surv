<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Models\CentralUser;
use App\Models\Tenant;
use App\User;

trait LoginToSubDomain
{   
    /**
     * Login to subdomain through UserImpersonate
     *
     * @param  \App\Models\Tenant $tenant null
     * @param  String $email null
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginToSubDomain(Tenant $tenant = null, String $email = null)
    { 
        return redirect( $this->loginLinkToSubDomain( $tenant, $email ) );
    }

    /**
     * Login to subdomain through UserImpersonate
     *
     * @param  \App\Models\Tenant $tenant null
     * @param  String $email null
     * @return String
     */
    public function loginLinkToSubDomain(Tenant $tenant = null, String $email = null)
    {   
        $email = $email ?? auth()->user()->email;

        // find owner in central app
        $centralUser = $this->getCentralUser( $email );

        // choose tenant
        $tenant = $tenant ?? $centralUser->tenants->first();

        // redirect to subdomain login link
        return $this->getSubDomainLink( $tenant, $email );
    }

    /**
     * Get owner account in central app (jobtron DB)
     *
     * @param  String $email
     * @return \App\Models\CentralUser|null
     */
    protected function getCentralUser(String $email)
    {
        $centralUser = CentralUser::with('tenants')
            ->where('email', $email)
            ->first();

        if( !$centralUser ) {
            throw new \Exception('Can\'t login '. auth()->user()->email . '. Owner account was not found in central app (Jobtron DB)');
        }

        return $centralUser;
    }

    /**
     * Get link for redirect to subdomain
     *
     * @param  \App\Models\Tenant $tenant
     * @param  String $email
     * @return String
     */
    protected function getSubDomainLink(Tenant $tenant, String $email)
    {   
        // target
        $subdomain = $tenant->id .".". config('app.domain');

        // initialize tenant
        tenancy()->initialize( $tenant );
        
        // find user in tenant app
        $tenantUser = User::where('email', $email)->first();

        if( !$tenantUser ) {
            throw new \Exception('Can\'t login with '. $email . ' in ' . $subdomain .' . Owner account was not found in tenant app (Tenant'. $tenant->id .' DB)');
        }

        // redirect link to subdomain  
        $token = tenancy()->impersonate($tenant, $tenantUser->id, '/profile');

        return "https://{$subdomain}/impersonate/{$token->token}";
    }
}
