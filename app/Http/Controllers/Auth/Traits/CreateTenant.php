<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Models\CentralUser;
use App\Models\Portal\Portal;
use App\Models\Tenant;
use App\Service\Tenancy\CabinetService;
use App\User;
use App\UserDescription;

trait CreateTenant
{
    /**
     * Login to subdomain through UserImpersonate
     *
     * @param  \App\User $user "users" table from Central app 
     * 
     * @return \App\Models\Tenant
     */
    public function createTenant(User $user)
    {   
        $tenant = $this->createTenantWithDomain($user);

        $this->createTenantUser($tenant, $user);

        return $tenant;
    }

    /**
     * Create tenant and attach it to owner
     *
     * @param  User $user
     * @return Tenant
     */
    protected function createTenantWithDomain(User $user)
    {
        // attach to cabinet as owner
        $centralUser = CentralUser::where('email', $user->email)->first();

        if(!$centralUser) {
            throw new \Exception();
        }
        $domain = $this->generateRandomName();
        
        // create tenant
        $tenant = Tenant::create(['id' => $domain]);

        // create domain
        $tenant->createDomain($domain);

        $centralUser->tenants()->attach($tenant);

        (new CabinetService)->add($tenant->id, $user, true);

        Portal::create([
            'tenant_id' => $tenant->id,
            'owner_id' => $user->id,
        ]);

        $mail = new \App\Mail\PortalCreatedMail([
            'name' => $centralUser->name,
        ]);
        \Mail::to($centralUser->email)->send($mail);
     
        return $tenant;
    }

    /**
     * Create a new user in tenant
     *
     * @param Tenant $tenant
     * @param User $user
     * @return User
     */
    protected function createTenantUser(Tenant $tenant, User $user)
    {
        tenancy()->initialize($tenant);

        $user = User::create([
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user['email'],
            'phone' => $user['phone'],
            'currency' => $user['currency'],
            'password' => $user['password'],
            'position_id' => 1,
            'program_id' => 1,
            'is_admin' => 1
        ]);
        
        $ud = UserDescription::create([
            'is_trainee' => 0,
            'user_id'    => $user->id
        ]);

        return $user;
    }

    /**
     * Generate unique subdomain name
     *
     * @return String $domain
     */
    protected function generateRandomName()
    {   
        $nameFound = false;

        do {

            $domain = $this->generateRandomString(6);

            if(Tenant::where('id', $domain)->doesntExist()) {
                $nameFound = true;
            }

        } while($nameFound == false);

        return $domain;
    }

    /**
     * Generate random string
     *
     * @param int $length
     * @return String
     */
    protected function generateRandomString(int $length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
