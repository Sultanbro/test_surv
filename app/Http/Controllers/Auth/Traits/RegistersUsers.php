<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Models\CentralUser;
use App\Models\Tenant;
use App\User;
use App\UserDescription;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        if(request()->getHost() !== config('app.domain')) {
            return redirect()->back();
        }
        
        event(new Registered($user = $this->createCentralUser($request->all())));

        if( !$user->hasTenant() ) {

            $tenant = $this->createTenant($user);
            $this->createTenantUser($tenant, $user);
        } 

        if($subDomainLink = $this->loginLink($user)) {

            // $this->guard()->login($user);
            
            return response()->json([
                'location' => $subDomainLink
            ]);
        }
        
        // Native Laravel code
        
        // if ($response = $this->registered($request, $user)) {
        //     return $response;
        // }

        // return $request->wantsJson()
        //             ? new JsonResponse([], 201)
        //             : redirect($this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function createCentralUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => \Hash::make($data['password']),
        ]);
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
            'password' => $user['password'],
            'is_admin' => 1
        ]);

        $ud = UserDescription::create([
            'is_trainee' => 0,
            'user_id'    => $user->id
        ]);
        
        return $user;
    }


    /**
     * Авторизоваться и вернуть ссылку на кабинет
     * 
     * @return String
     */
    protected function loginLink(User $user)
    {
        $centralUser = CentralUser::with('tenants')->where('email', $user->email)->first();

        if($centralUser) {

            // Get first tenant
            //
            // if user has more than one tenant and wants to change tenant
            // he easily can do it by special select in frontend
            // that gives this opportunity 
            $tenant = $centralUser->tenants->first();

            // find Owners User record in tenant
            tenancy()->initialize($tenant);

            $tenantUser = User::where('email', $centralUser->email)->first();

            // create token to login through redirect
            $token = tenancy()->impersonate($tenant, $tenantUser->id, '/profile');

            return "https://{$tenant->id}.".config('app.domain')."/impersonate/{$token->token}";
        }  

        throw new \Exception('Something\'s gone wrong. Tenant created, but has not owner.');
    }

    /**
     * Create tenant and attach it to owner
     *
     * @param  User $user
     * @return Tenant
     */
    protected function createTenant(User $user)
    {   
        $domain = $this->generateRandomName();
        
        // create tenant
        $tenant = Tenant::create(['id' => $domain]);

        // create domain
        $tenant->createDomain($domain);

        // attach to owner
        $centralUser = CentralUser::where('email', $user->email)->first();

        if($centralUser) {
            $centralUser->tenants()->attach($tenant);
        }   
     
        return $tenant;
    }

    /**
     * Generate unique subdomain name
     *
     * @return String $domain
     */
    private function generateRandomName()
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
    private function generateRandomString(int $length = 10)
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
