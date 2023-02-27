<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Api\BitrixOld\RegistrationLead;
use Illuminate\Foundation\Auth\RedirectsUsers;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Auth\Traits\CreateTenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Exception;

trait RegistersUsers
{
    use RedirectsUsers, LoginToSubDomain, CreateTenant;

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
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
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
            'currency' => $data['currency'],
            'password' => \Hash::make($data['password']),
        ]);
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

        // find tenant
        $tenant = $user->tenants()->first() ?? $this->createTenant($user);

        $this->createRegistrationLead($user);

        // link
        return response()->json([
            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
        ]);
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

    private function createRegistrationLead(User $user) {
        try {
            (new RegistrationLead($user, null))
                ->setNeedCallback(false)
                ->publish();
        } catch(Exception $err) {
            return; //TODO add logs
        }
    }
}
