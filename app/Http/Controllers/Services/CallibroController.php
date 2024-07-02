<?php

namespace App\Http\Controllers\Services;

use Illuminate\Support\Str;
use App\User;
use App\Http\Controllers\Controller;

class CallibroController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function login()
    {   
        $remember_token = auth()->user()->remember_token;

        if ($remember_token === null
         || $remember_token === '') {
          
            $remember_token = Str::random(60);
            
            $user = User::find(auth()->id());
            $user->remember_token = $remember_token;
            $user->save();
        }

        return redirect('https://cp.callibro.org/setting/bp/auth/' . $remember_token . '?route=/obzvon/dashboard');
    }
}
