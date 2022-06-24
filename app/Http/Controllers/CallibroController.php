<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        return redirect('https://cp.callibro.org/setting/bp/auth/' . auth()->user()->remember_token . '?route=/obzvon/dashboard');
    }

}
