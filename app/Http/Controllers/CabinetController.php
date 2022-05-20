<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\User;
use App\Admin;
use DB;

class CabinetController extends Controller
{
    public function index()
    {
        View::share('menu', 'cabinet');
        View::share('link', 'cabinet');

        return view('cabinet');
    }

    public function get()
    {
        $users = User::get(['ID', DB::raw("CONCAT(NAME,' ',LAST_NAME) as EMAIL")]);

        foreach($users as $user) {
            if($user->EMAIL == '') $user->EMAIL = 'x'; 
        }

        $admin = Admin::where('owner_id', 18)->first(); 
        $admins = User::withTrashed()->whereIn('ID', $admin->users)->get(['ID', DB::raw("CONCAT(NAME,' ',LAST_NAME) as EMAIL")]);

        return [
            'users' => $users,
            'admins' => $admins
        ];
    }

    public function save(Request $request)
    {
        $admins = [];
        foreach($request->admins as $index => $user) {
            $admins[] = $user['ID'];
        }
        $admin = Admin::where('owner_id', 18)->first(); 
        $admin->users = array_unique($admins);
        $admin->save();
        
    }
}
