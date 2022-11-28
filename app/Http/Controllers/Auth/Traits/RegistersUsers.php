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

        //  $this->guard()->login($user);

        if($user->hasTenant()) {
            // redirect to first tenant to auth User impersonation
            return redirect('/');
        } 

        $tenant = $this->createTenant($user);

        tenancy()->initialize($tenant);
        
        $this->createTenantUser($user);
        
        return $this->login($user); 

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
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
     * @return \App\User
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
     * @param  array  $data
     * @return \App\User
     */
    protected function createTenantUser(User $user)
    {
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
     * Create a new user in tenant
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function createTenant(User $user)
    {   
        $nameFound = false;

        do {

            $domain = $this->generateRandomString(12);

            if(Tenant::where('id', $domain)->doesntExist()) {
                $nameFound = true;
            }

        } while($nameFound == false);
        
        $tenant = Tenant::create(['id' => $domain]);

        $tenant->createDomain($domain);

        $centralUser = CentralUser::where('email', $user->email)->first();

        if($centralUser) {
            $centralUser->tenants()->attach($tenant);
        }   
     
        return $tenant;
    }

    /**
     * Create a new user in tenant
     *
     * @param int $length
     * @return String
     */
    protected function generateRandomString(int $length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    protected function login(User $user) {
        $centralUser = CentralUser::with('tenants')->where('email', $user->email)->first();

        if($centralUser) {

            $tenant = $centralUser->tenants->first();

            tenancy()->initialize($tenant);
            
            $domain = $tenant->id .".". config('app.domain');

            $tenantUser = User::where('email', $centralUser->email)->first();

            $token = tenancy()->impersonate($tenant, $tenantUser->id, '/profile');

            return redirect("https://". $domain ."/impersonate/{$token->token}");
        }   
    }

    
}
