<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use App\User;
use App\Admin;
use DB;

class CallibroController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function login()
    {   
        // if(auth()->user()->id != 5) abort(404);

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
