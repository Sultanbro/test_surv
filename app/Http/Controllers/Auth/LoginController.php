<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CentralUser;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'profile';
   // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $login = request()->input('username');

        if(is_numeric($login)){
            $field = 'phone';
        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } 
        else {
            $field = 'email';
        }

        request()->merge([$field => $login]);

        return $field;
    }


    public function login(Request $request)
    {   
        $field = $this->username();

        $request[$field] = $request->username;

        $credentials = [
            $field => $request[$field],
            'password' => $request->password,
        ];
        

        if (\Auth::attempt($credentials)) {

            $request->session()->regenerate();
            
            // admin.jobtron.org
            if(request()->getHost() == 'admin.' .config('app.domain')) {
                return redirect('/');
            }

            // login in central app
            if(request()->getHost() == config('app.domain')) {
                
                $centralUser = CentralUser::with('tenants')->where('email', auth()->user()->email)->first();

                if($centralUser) {

                    $tenant = $centralUser->tenants->first();

                    tenancy()->initialize($tenant);
                   
                    $domain = $tenant->id .".". config('app.domain');

                    $tenantUser = User::where('email', $centralUser->email)->first();

                    $token = tenancy()->impersonate($tenant, $tenantUser->id, '/profile');
        
                    return redirect("https://". $domain ."/impersonate/{$token->token}");
                }   

            } 
            
            return redirect($this->redirectTo);

        } else {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }   
        
     
       
    }
}

