<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PermissionController extends Controller
{
    public function index(Request $request)
    {   
        View::share('menu', 'permissions');
        View::share('link', 'permissions');

        return view('admin.permissions');
    }

    public function get(Request $request)
    {   
        return [];
    }
}
