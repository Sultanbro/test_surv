<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Controller;
use App\Models\CentralUser;
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
    use AuthenticatesUsers, LoginToSubDomain;

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
        $this->middleware('guest')
            ->except('logout');
    }


    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request $request
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

        if (is_numeric($login)) {
            $field = 'phone';
        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } else {
            $field = 'email';
        }

        request()->merge([$field => $login]);

        return $field;
    }

    /**
     * Login
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // create credentials
        $field = $this->username();

        $request[$field] = $request->username;

        $credentials = [
            $field => $request[$field],
            'password' => $request->password,
        ];
        // failed to login
        if (!\Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Введенный email или пароль не совпадает'
            ], 401);
        }

        // record login time
        $user = CentralUser::query()->where($field, $credentials[$field])->first();
        $user?->update([
            'login_at' => now()
        ]);

        // login was success
        $request->session()->regenerate();

        // redirect to - admin.jobtron.org
        if (request()->getHost() == 'admin.' . config('app.domain')) {
            return response()->json([
                'link' => $this->redirectTo
            ]);
        }

        // login from central app  - jobtron.org/login
        // redirect to subdomain with auth
        if (request()->getHost() == config('app.domain')) {
            $links = $this->loginLinks($request->email);

            $data = [];
            if (!empty($links)) {

                $data = count($links) > 1
                    ? [
                        'links' => $links
                    ]
                    : [
                        'link' => $links[0]['link']
                    ];
            }

            return response()->json($data);
        }

        // login from tenant app
        return response()->json([
            'link' => $this->redirectTo
        ]);
    }
}

